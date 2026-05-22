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
            'type' => 'required|in:official,personal',
            'assigned_to' => 'nullable|exists:users,id' // Validasi ID user
        ]);

        $user = Auth::user();
        $isPic = $event->participants()->where('user_id', $user->id)->where('is_pic', true)->exists();
        $isAdmin = $user->hasRole(['CEO', 'GM']);

        if ($request->type === 'official' && !$isPic && !$isAdmin) {
            return response()->json(['error' => 'Hanya PIC atau Manajemen yang bisa membuat To Do event.'], 403);
        }

        $task = new EventTask();
        $task->event_id = $event->id;
        $task->task_name = $request->task_name;
        $task->category = $request->category;
        $task->type = $request->type;
        $task->created_by = $user->id;
        
        // PERBAIKAN LOGIKA PENUGASAN:
        if ($request->type === 'personal') {
            $task->assigned_to = $user->id;
        } else {
            // Cek apakah ada assigned_to yang dikirim, jika ada simpan, jika tidak (Umum) biarkan null
            $task->assigned_to = $request->filled('assigned_to') ? $request->assigned_to : null;
        }
        
        $task->save();

        $task->load('assignee');

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
        $isAdmin = $user->hasRole(['CEO', 'GM']);

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
        $isAdmin = $user->hasRole(['CEO', 'GM']);

        if ($task->created_by !== $user->id && !$isPic && !$isAdmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'completion_percentage' => $event->official_tasks_percentage
        ]);
    }
}
