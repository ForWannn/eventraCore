<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRecap;
use App\Models\EventRecapItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EventRecapController extends Controller
{
    /**
     * Check if user belongs to Finance division.
     */
    private function isFinance(User $user): bool
    {
        return optional($user->division)->name === 'Finance';
    }

    /**
     * Check if user is PIC of the event.
     */
    private function isPic(User $user, Event $event): bool
    {
        return $event->participants()
            ->where('user_id', $user->id)
            ->where('event_participants.is_pic', true)
            ->exists();
    }

    /**
     * Display a listing of event recaps.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isHistory = $request->routeIs('event-recaps.history');
        
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $search = $request->input('search', '');
        
        $searchPattern = '%"' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-%';

        $isPicOfAny = $user->events()->wherePivot('is_pic', true)->exists();
        $hasPermission = $user->can('rekap_event') || optional($user->division)->name === 'Finance';

        if (!$isPicOfAny && !$hasPermission) {
            abort(403, 'Anda tidak memiliki akses ke rekapitulasi event.');
        }

        // Base query
        $query = Event::with(['participants', 'recap', 'recapItems'])
            ->where('event_dates', 'like', $searchPattern);

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Access Control
        if ($hasPermission) {
            // Users with rekap_event permission see all events
            if ($isHistory) {
                $query->whereHas('recap', fn($q) => $q->where('status', 'selesai'));
            } else {
                $query->where(function ($q) {
                    $q->whereDoesntHave('recap')
                      ->orWhereHas('recap', fn($sub) => $sub->where('status', '!=', 'selesai'));
                });
            }
        } else {
            // PIC Event sees only their assigned events
            $query->whereHas('participants', function ($q) use ($user) {
                $q->where('event_participants.user_id', $user->id)->where('event_participants.is_pic', true);
            });

            if ($isHistory) {
                $query->whereHas('recap', fn($q) => $q->where('status', 'selesai'));
            } else {
                $query->where(function ($q) {
                    $q->whereDoesntHave('recap')
                      ->orWhereHas('recap', fn($sub) => $sub->where('status', '!=', 'selesai'));
                });
            }
        }

        $events = $query->orderBy('id', 'desc')->paginate(10);

        return view('event-recaps.index', compact('events', 'month', 'year', 'isHistory', 'search'));
    }

    /**
     * Display the specified event recap.
     */
    public function show(Event $event, Request $request)
    {
        $user = Auth::user();
        
        // Authorization check
        $isFinance = $this->isFinance($user);
        $isPic = $this->isPic($user, $event);
        $hasPermission = $user->can('rekap_event') || $isFinance;

        if (!$isPic && !$hasPermission) {
            abort(403, 'Anda tidak memiliki akses ke rekapitulasi event ini.');
        }

        // Treat users with permission who are not Finance as read-only (like CEO/GM)
        $isLeader = $user->hasAnyRole(['CEO', 'GM']) || ($hasPermission && !$isFinance);

        // Initialize recap if not exists
        $recap = $event->recap;
        if (!$recap) {
            $recap = EventRecap::create([
                'event_id' => $event->id,
                'initial_nominal' => 0,
                'expected_receipts_count' => 10,
                'status' => 'draft',
            ]);
        }

        // Get active tab (default to recap)
        $activeTab = $request->input('tab', 'recap');
        
        // Sorting and filters for expenditures
        $searchQuery = $request->input('search_item', '');
        $categoryFilter = $request->input('category', 'all');
        $sortBy = $request->input('sort', 'latest');

        $itemsQuery = $event->recapItems()->with('uploader');

        if (!empty($searchQuery)) {
            $itemsQuery->where(function ($q) use ($searchQuery) {
                $q->where('vendor', 'like', '%' . $searchQuery . '%')
                  ->orWhere('description', 'like', '%' . $searchQuery . '%');
            });
        }

        if ($categoryFilter !== 'all') {
            $itemsQuery->where('category', $categoryFilter);
        }

        switch ($sortBy) {
            case 'oldest':
                $itemsQuery->orderBy('date', 'asc');
                break;
            case 'nominal_desc':
                $itemsQuery->orderBy('nominal', 'desc');
                break;
            case 'nominal_asc':
                $itemsQuery->orderBy('nominal', 'asc');
                break;
            case 'latest':
            default:
                $itemsQuery->orderBy('date', 'desc');
                break;
        }

        $items = $itemsQuery->get();

        // Get PIC details
        $picDetails = $event->participants()->where('is_pic', true)->first();

        // Calculations
        $totalSpent = $recap->total_spent;
        $remainingBudget = $recap->remaining_budget;
        $completionScore = $recap->completion_score;

        return view('event-recaps.show', compact(
            'event',
            'recap',
            'items',
            'picDetails',
            'isFinance',
            'isLeader',
            'isPic',
            'activeTab',
            'totalSpent',
            'remainingBudget',
            'completionScore',
            'searchQuery',
            'categoryFilter',
            'sortBy'
        ));
    }

    /**
     * Update budget parameters (Finance only).
     */
    public function updateBudget(Request $request, Event $event)
    {
        $user = Auth::user();
        if (!$this->isFinance($user) || !$user->can('rekap_event')) {
            abort(403, 'Hanya tim Finance dengan akses rekap yang dapat mengatur anggaran.');
        }

        $request->validate([
            'initial_nominal' => 'required|numeric|min:0',
        ]);

        $recap = $event->recap ?: new EventRecap(['event_id' => $event->id]);
        
        $oldNominal = (float) $recap->initial_nominal;
        $newNominal = (float) $request->initial_nominal;

        if ($oldNominal > 0 && $newNominal !== $oldNominal) {
            $diff = $newNominal - $oldNominal;
            if ($diff > 0) {
                // Penambahan anggaran
                EventRecapItem::create([
                    'event_id' => $event->id,
                    'date' => now()->format('Y-m-d'),
                    'category' => 'Pemasukan',
                    'item_name' => 'Penyesuaian Anggaran (Otomatis)',
                    'vendor' => 'Finance',
                    'quantity' => 1,
                    'unit_price' => $diff,
                    'nominal' => $diff,
                    'description' => 'Penambahan anggaran otomatis oleh tim Finance',
                    'notes' => 'Pemberian anggaran tambahan oleh Finance',
                    'receipt_path' => '',
                    'uploader_id' => $user->id,
                ]);
            } else {
                // Pengurangan anggaran
                EventRecapItem::create([
                    'event_id' => $event->id,
                    'date' => now()->format('Y-m-d'),
                    'category' => 'Pengurangan Anggaran',
                    'item_name' => 'Penyesuaian Anggaran (Otomatis)',
                    'vendor' => 'Finance',
                    'quantity' => 1,
                    'unit_price' => abs($diff),
                    'nominal' => abs($diff),
                    'description' => 'Pengurangan anggaran otomatis oleh tim Finance',
                    'notes' => 'Pengurangan anggaran oleh Finance',
                    'receipt_path' => '',
                    'uploader_id' => $user->id,
                ]);
            }
        }

        $recap->initial_nominal = $newNominal;

        // If status was draft, transition to dalam_rekap since budget is set
        if ($recap->status === 'draft') {
            $recap->status = 'dalam_rekap';
        }

        $recap->save();

        return redirect()->route('event-recaps.show', $event->id)->with('success', 'Anggaran berhasil diperbarui.');
    }

    /**
     * Store a new recap expenditure item (PIC only).
     */
    public function storeItem(Request $request, Event $event)
    {
        $user = Auth::user();
        if (!$this->isPic($user, $event)) {
            abort(403, 'Hanya PIC event yang dapat menambahkan nota.');
        }

        $recap = $event->recap;
        if (!$recap || in_array($recap->status, ['menunggu_finance', 'selesai'])) {
            return redirect()->back()->with('error', 'Tidak dapat menambahkan nota.');
        }

        // 1. Validasi (tambahkan kategori baru jika perlu)
        $request->validate([
            'date' => 'required|date',
            'category' => 'required|string', 
            'vendor' => 'required|string|max:255',
            'item_name' => 'required|string|max:255', // Kolom baru
            'quantity' => 'required|integer|min:1',   // Kolom baru
            'unit_price' => 'required|numeric|min:0', // Kolom baru
            'description' => 'nullable|string',
            'notes' => 'nullable|string',             // Kolom baru
            'receipt' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if (in_array(strtolower(trim($request->category)), ['pemasukan', 'pengurangan anggaran', 'penyesuaian anggaran'])) {
            return redirect()->back()->with('error', 'Kategori ini hanya dapat ditambahkan secara otomatis oleh sistem/Finance.');
        }

        if ($request->item_name === 'Penyesuaian Anggaran (Otomatis)') {
            return redirect()->back()->with('error', 'Nama item ini dicadangkan untuk penyesuaian anggaran otomatis.');
        }

        // 2. Upload ke Storage (Menggunakan storage/app/public)
        $filename = 'receipt_' . $event->id . '_' . time() . '_' . Str::random(5) . '.' . $request->file('receipt')->getClientOriginalExtension();
        // Simpan ke storage/app/public/receipts
        $path = $request->file('receipt')->storeAs('receipts', $filename, 'public');

        // 3. Simpan ke Database
        EventRecapItem::create([
            'event_id' => $event->id,
            'date' => $request->date,
            'category' => $request->category,
            'vendor' => $request->vendor,
            'item_name' => $request->item_name,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'nominal' => $request->quantity * $request->unit_price, // Total
            'description' => $request->description,
            'notes' => $request->notes,
            'receipt_path' => $path, // Contoh isi: 'receipts/namafile.jpg'
            'uploader_id' => $user->id,
        ]);

        if (in_array($recap->status, ['draft', 'direvisi'])) {
            $recap->update(['status' => 'dalam_rekap']);
        }

        return redirect()->route('event-recaps.show', $event->id)->with('success', 'Berhasil.');
    }

    /**
     * Delete an expenditure item (PIC only).
     */
    public function destroyItem(Event $event, EventRecapItem $item)
    {
        $user = Auth::user();
        if (!$this->isPic($user, $event)) {
            abort(403, 'Hanya PIC event yang dapat menghapus nota.');
        }

        $recap = $event->recap;
        if (!$recap || in_array($recap->status, ['menunggu_finance', 'selesai'])) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus nota. Rekap sedang ditinjau atau telah diselesaikan.');
        }

        if ($item->event_id !== $event->id) {
            abort(404);
        }

        if ($item->item_name === 'Penyesuaian Anggaran (Otomatis)') {
            return redirect()->back()->with('error', 'Tidak dapat menghapus item penyesuaian anggaran otomatis.');
        }

        // Delete physical file
        $filePath = public_path($item->receipt_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $item->delete();

        return redirect()->route('event-recaps.show', $event->id)->with('success', 'Nota belanja berhasil dihapus.');
    }

    /**
     * Submit recap to Finance (PIC only).
     */
    public function submitToFinance(Event $event)
    {
        $user = Auth::user();
        if (!$this->isPic($user, $event)) {
            abort(403, 'Hanya PIC event yang dapat menyelesaikan rekap.');
        }

        $recap = $event->recap;
        if (!$recap || in_array($recap->status, ['menunggu_finance', 'selesai'])) {
            return redirect()->back()->with('error', 'Status rekap saat ini tidak mengizinkan pengiriman.');
        }

        $recap->update([
            'status' => 'menunggu_finance'
        ]);

        return redirect()->route('event-recaps.show', $event->id)->with('success', 'Seluruh rekapitulasi berhasil dikirim ke tim Finance untuk diverifikasi.');
    }

    /**
     * Approve recap (Finance only).
     */
    public function approveRecap(Event $event)
    {
        $user = Auth::user();
        if (!$this->isFinance($user) || !$user->can('rekap_event')) {
            abort(403, 'Hanya tim Finance dengan akses rekap yang dapat menyelesaikan verifikasi rekap.');
        }

        $recap = $event->recap;
        if (!$recap || $recap->status === 'selesai') {
            return redirect()->back()->with('error', 'Rekap sudah dalam status Selesai.');
        }

        // Calculate Ketepatan Waktu & Speed Percentage
        $speedPct = 20; // fallback minimum
        $completedAt = now();

        $eventDates = $event->event_dates ?? [];
        if (!empty($eventDates)) {
            sort($eventDates);
            $eventEnd = Carbon::parse(end($eventDates))->endOfDay();
            if ($event->end_time) {
                $eventEnd = Carbon::parse(end($eventDates))->setTimeFromTimeString((string) $event->end_time);
            }

            if ($completedAt <= $eventEnd) {
                $speedPct = 100;
            } else {
                $hoursLate = $eventEnd->diffInHours($completedAt);
                if ($hoursLate <= 24) {
                    $speedPct = 100;
                } elseif ($hoursLate <= 48) {
                    $speedPct = 80;
                } elseif ($hoursLate <= 72) {
                    $speedPct = 60;
                } elseif ($hoursLate <= 96) {
                    $speedPct = 40;
                } else {
                    $speedPct = 20;
                }
            }
        }

        $recap->update([
            'status' => 'selesai',
            'completed_at' => $completedAt,
            'speed_percentage' => $speedPct
        ]);

        return redirect()->route('event-recaps.show', $event->id)->with('success', 'Rekapitulasi telah diverifikasi dan ditandai Selesai.');
    }

    /**
     * Reopen recap / Rekap Tambahan (Finance only).
     */
    public function reopenRecap(Request $request, Event $event)
    {
        $user = Auth::user();
        if (!$this->isFinance($user) || !$user->can('rekap_event')) {
            abort(403, 'Hanya tim Finance dengan akses rekap yang dapat membuka kembali rekap.');
        }

        $recap = $event->recap;
        if (!$recap) {
            abort(404);
        }

        $recap->update([
            'status' => 'direvisi', // Set status to 'direvisi' so PIC can upload again
            'completed_at' => null,
            'speed_percentage' => null
        ]);

        return redirect()->route('event-recaps.show', $event->id)->with('success', 'Halaman rekapitulasi telah dibuka kembali untuk PIC (Status: Direvisi).');
    }

    /**
     * Export recap to Excel format (Finance and CEO/GM only).
     */
    // public function export(Event $event)
    // {
    //     $user = Auth::user();
    //     $isFinance = $this->isFinance($user);
    //     $isLeader = $user->hasAnyRole(['CEO', 'GM']);
    //     $hasPermission = $user->can('rekap_event') || $isFinance;

    //     if ((!$isFinance && !$isLeader) || !$hasPermission) {
    //         abort(403, 'Anda tidak memiliki hak untuk mengekspor dokumen rekap.');
    //     }

    //     $recap = $event->recap;
    //     if (!$recap) {
    //         abort(404, 'Rekapitulasi belum diinisialisasi.');
    //     }

    //     $items = $event->recapItems()->orderBy('date', 'asc')->get();
    //     $picDetails = $event->participants()->where('is_pic', true)->first();

    //     // Export filename
    //     $filename = 'Rekap_Event_' . Str::slug($event->name) . '_' . date('Ymd_His') . '.xls';

    //     // Return view as an Excel download
    //     return response()->view('event-recaps.export', [
    //         'event' => $event,
    //         'recap' => $recap,
    //         'items' => $items,
    //         'picDetails' => $picDetails,
    //     ])
    //     ->header('Content-Type', 'application/vnd.ms-excel')
    //     ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    // }
    public function export(\App\Models\Event $event)
    {
        $event->load(['recap']);
        $items = $event->recapItems()->orderBy('date', 'asc')->get();
        
        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $event->name);
        $fileName = 'Rekap_Pengeluaran_Ops_' . $cleanName . '.xls';

        return response()->view('event-recaps.export', [
            'event' => $event,
            'recap' => $event->recap,
            'items' => $items
        ])
        ->header('Content-Type', 'application/vnd.ms-excel')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
    }
}
