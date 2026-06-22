<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventTaskController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'category' => 'required|in:pre,dday,post',
            'assigned_to' => 'required|exists:users,id'
        ]);

        $user = Auth::user();
        $isPic = $event->participants()->where('user_id', $user->id)->where('is_pic', true)->exists();
        $isAdmin = $user->hasRole(['Director', 'Direktur', 'GM']);

        if ($request->assigned_to != $user->id && !$isPic && !$isAdmin) {
            return response()->json(['error' => 'Anda hanya bisa menambahkan To Do untuk diri sendiri.'], 403);
        }

        $task = new EventTask();
        $task->event_id = $event->id;
        $task->task_name = $request->task_name;
        $task->category = $request->category;
        $task->type = 'official';
        $task->created_by = $user->id;
        $task->assigned_to = $request->assigned_to;
        $task->save();

        $task->load('assignee');

        // Send WhatsApp notification
        if ($task->assignee && !empty($task->assignee->phone)) {
            $url = url('/');
            $message = "[INFO PENUGASAN EVENT]\n\n"
                     . "Halo {$task->assignee->name},\n"
                     . "Terdapat penambahan tugas baru pada event {$event->name}.\n\n"
                     . "Harap segera mengecek rincian dan tenggat waktu tugas tersebut di sistem:\n"
                     . "{$url}\n\n"
                     . "Terima kasih!";
            \App\Services\FonnteService::send($task->assignee->phone, $message);
        }

        return response()->json([
            'success' => true, 
            'task' => $task,
            'completion_percentage' => $event->official_tasks_percentage
        ]);
    }

    public function toggleComplete(EventTask $task)
    {
        $user = Auth::user();
        $event = $task->event;
        $isPic = $event->participants()->where('user_id', $user->id)->where('is_pic', true)->exists();
        $isAdmin = $user->hasRole(['Director', 'Direktur', 'GM']);

        // PIC, Admin, or the assignee can toggle
        if ($user->id !== $task->assigned_to && !$isPic && !$isAdmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->is_completed = !$task->is_completed;
        $task->save();

        return response()->json([
            'success' => true, 
            'is_completed' => $task->is_completed,
            'completion_percentage' => $event->official_tasks_percentage
        ]);
    }

    public function destroy(EventTask $task)
    {
        $user = Auth::user();
        $event = $task->event;
        $isPic = $event->participants()->where('user_id', $user->id)->where('is_pic', true)->exists();
        $isAdmin = $user->hasRole(['Director', 'Direktur', 'GM']);

        if ($task->created_by !== $user->id && $task->assigned_to !== $user->id && !$isPic && !$isAdmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'completion_percentage' => $event->official_tasks_percentage
        ]);
    }
}
