<div class="task-item {{ $task->is_completed ? 'completed' : '' }}" id="task-item-{{ $task->id }}">
    <div class="task-checkbox {{ $task->is_completed ? 'checked' : '' }}" 
         onclick="toggleTask('{{ $task->id }}')" 
         id="checkbox-{{ $task->id }}">
        @if($task->is_completed)
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        @endif
    </div>
    
    <div class="task-text">{{ $task->task_name }}</div>
    
    <div style="display: flex; align-items: center; gap: 6px;">
        <span class="task-type-badge task-{{ $task->type }}">
            {{ $task->type === 'official' ? 'To Do' : 'Personal' }}
        </span>

        @if($task->assignee)
            <img src="{{ $task->assignee->photo_url }}" 
                 class="task-assignee" 
                 title="Ditugaskan ke {{ $task->assignee->name }}">
        @endif

        @if($task->created_by === auth()->id() || (isset($isPic) && $isPic) || (isset($isLeader) && $isLeader))
            <span class="task-delete-btn" onclick="deleteTask('{{ $task->id }}')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
            </span>
        @endif
    </div>
</div>
