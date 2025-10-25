@foreach ($projects as $project)
    @php
        /* --- LOGIKA DATA PROJECT (PHP) --- */
        // Ambil Status & Progress (Fallbacks untuk Project Baru)
        $currentStatus = $project->status ?? 'Draft'; 
        $currentProgress = $project->completion_progress ?? 0;
        
        // Tentukan kelas CSS berdasarkan status
        $statusClass = match ($currentStatus) {
            'Done' => 'done',
            'In Progress' => 'in-progress',
            default => 'draft',
        };
     
        $badgeClass = match ($currentStatus) {
            'Done' => 'success',
            'In Progress' => 'warning',
            default => 'secondary',
        };
    @endphp

    {{-- PROJECT CARD (DOM Anchor) --}}
    <div id="project-{{ $project->id }}" class="card shadow mb-4 card-project {{ $statusClass }}"> 
        <div class="card-body">
            <div  onclick="editProject({{ $project->id }}, '{{ $project->name }}')" style="cursor: pointer;">

                    {{-- HEADER NAMA & TOMBOL ADD TASK --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title  mb-0">{{ $project->name }}</h4>
                        
                        <button onclick="showAddTaskModal({{ $project->id }}, '{{ $project->name }}'); event.stopPropagation();" 
                            class="btn btn-lg text-success p-0 pe-3 border-0" 
                            title="Add Task" 
                            onmouseover="this.style.transform='scale(1.1)'; this.style.color='#157347'" 
                            onmouseout="this.style.transform='scale(1)'; this.style.color='#198754'">
                        <i class="bi bi-plus-circle fs-3"></i> 
                    </button>
                </div>
                
                {{-- DISPLAY STATUS BADGE & PROGRESS BAR --}}
                <p class="card-text mb-2 text-primary fw-semibold">
                    Status: <span class="badge bg-{{ $badgeClass }}">{{ $currentStatus }}</span>
                </p>
                
                <div class="progress mb-3" role="progressbar" aria-label="Project Progress" aria-valuenow="{{ $currentProgress }}" aria-valuemin="0" aria-valuemax="100" style="height: 20px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: {{ $currentProgress }}%">
                        {{ $currentProgress }}%
                    </div>
                </div>
            </div>

            {{-- TASK LIST CONTAINER  --}}
            <div id="tasks-list-{{ $project->id }}" class="mt-3 border-top pt-3">
                @if($project->tasks->count() > 0)
                    <p class="text-primary fw-semibold">Tasks:</p>
                    <ul class="list-group list-group-flush">
                        @foreach ($project->tasks as $task)
                            @php
                                $statusColor = ['Draft' => 'secondary','In Progress' => 'warning','Done' => 'success',][$task->status] ?? 'secondary';
                            @endphp
                            
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent border-0 shadow-sm" id="task-{{ $task->id }}">
                                <div onclick="editTask({{ $task->id }}, {{ $task->project_id }}, '{{ $task->name }}', '{{ $task->status }}', {{ $task->weight }}, '{{ $project->name }}'); event.stopPropagation();" style="cursor: pointer; flex-grow: 1; transition: transform .15s ease;" onmouseover="this.style.transform='translateX(4px)';" onmouseout="this.style.transform='translateX(0)';">
                                    <p class="mb-0 fw-bold">{{ $task->name }}</p>
                                    <small class="text-muted">Bobot: {{ $task->weight }}</small>
                                </div>
                                <span class="badge bg-{{ $statusColor }} ms-3">
                                    {{ $task->status }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    {{-- Placeholder jika list Task kosong --}}
                    <p class="text-secondary fst-italic">Belum ada Tasks. Klik (+) untuk menambahkan.</p>
                @endif
            </div>
        </div>
    </div>
@endforeach