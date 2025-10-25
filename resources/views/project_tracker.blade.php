<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <title>Mini Project Tracker</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    /* 1. Base Styling */
    body {
        background-color: #f8f9fa; 
    }
    .progress-bar {
        transition: width 0.5s ease;
        height: 100%;
        background-color: #007bff; 
    }

    /* 2. Card Styling & Shadow */
    .card-project {
        border-radius: 12px; 
        overflow: hidden; 
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); 
        border: none;
    }
    
    /* 3. Status Indicators */
    .card-project.done { border-left: 6px solid #198754; } 
    .card-project.in-progress { border-left: 6px solid #ffc107; } 
    .card-project.draft { border-left: 6px solid #6c757d; } 

    /* 4. Progress Bar Customization */
    .progress {
        height: 15px !important; /* Membuat progress bar lebih tipis */
        background-color: #e9ecef;
        border-radius: 5px;
    }

    /* Override Footer Modal */
    #projectModal .modal-footer, #taskModal .modal-footer {
        display: flex;
        justify-content: space-between;
    }
</style>
</head>
<body>
    <div class="container my-5">
        
       <header class="mb-5 text-center pb-1 border-bottom">
            <h1 class="display-5 fw-bold text-dark">Mini Aplikasi Project Tracker</h1>
            <p class="lead text-muted mt-3">
                Kelola Project dan Task Anda dengan realtime update, mudah dan efisien.
            </p>
        </header>

        <div class="d-flex mb-4">
            <button class="btn btn-primary me-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#projectModal" data-mode="create" onclick="resetProjectModal()">
                <i class="bi bi-folder-plus me-1"></i> Add Project
            </button>
            <button class="btn btn-secondary" onclick="showAddTaskModalGlobal()">
                <i class="bi bi-list-task me-1"></i> Add Task
            </button>
        </div>

        {{-- CONTAINER DAFTAR PROJECT --}}
        <div id="projects-list">
            @include('partials.projects_table', ['projects' => $projects])
        </div>
    </div>

    {{-- MODAL SECTION --}}
    @include('partials.project_modal') 

    {{-- MODAL TASK SECTION --}}
    @include('partials.task_modal')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Setup AJAX CSRF Token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // VARIABEL GLOBAL UNTUK DATA PROJECT (DIBUTUHKAN UNTUK DROPDOWN)
        const ALL_PROJECTS = @json($projects->pluck('name', 'id'));


        // ==========================================================
        // FUNGSI HELPER
        // ==========================================================
        
        // Fungsi Helper untuk Update Status & Progress Project Card
        function updateProjectView(project) {
            let statusClass;
            let badgeClass; 
            
            // Tentukan kelas CSS berdasarkan status
            if (project.status === 'Done') {
                statusClass = 'done';
                badgeClass = 'success';
            } else if (project.status === 'In Progress') {
                statusClass = 'in-progress';
                badgeClass = 'warning';
            } else {
                statusClass = 'draft';
                badgeClass = 'secondary'; 
            }

            // Update DOM Project Card
            let $projectCard = $('#project-' + project.id);
           
            // Update Progress Bar
            $projectCard.find('.progress-bar').css('width', project.completion_progress + '%').text(project.completion_progress + '%');
            
            // Update Status Text dan Badge
            $projectCard.find('.card-text strong').text(project.status);             
            $projectCard.find('.card-text span.badge')
                .removeClass('bg-success bg-warning bg-secondary') 
                .addClass('bg-' + badgeClass) 
                .text(project.status);             
            $projectCard.removeClass('done in-progress draft').addClass(statusClass);
        }

        // Fungsi Helper untuk render Item Task baru
        function renderTaskRow(task) {

            // Tentukan warna badge berdasarkan status
            let statusColor;
            if (task.status === 'Done') {
                statusColor = 'success';
            } else if (task.status === 'In Progress') {
                statusColor = 'warning';
            } else {
                statusColor = 'secondary';
            }
            
            const projectName = ALL_PROJECTS[task.project_id];             
            updateProjectView(task.project); 

            // Return HTML string untuk Task
            return `
                <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent border-0 shadow-sm" id="task-${task.id}">
                    <div onclick="editTask(${task.id}, ${task.project_id}, '${task.name}', '${task.status}', ${task.weight}, '${projectName}'); event.stopPropagation();" style="cursor: pointer; flex-grow: 1; transition: transform .15s ease;" onmouseover="this.style.transform='translateX(4px)';" onmouseout="this.style.transform='translateX(0)';">
                        <p class="mb-0 fw-bold text-truncate">${task.name}</p>
                        <small class="text-muted">Bobot: ${task.weight}</small>
                    </div>
                    <span class="badge bg-${statusColor} ms-3">${task.status}</span>
                </li>
            `;
        }

        // Fungsi Helper untuk render Card Project Baru
        function renderProjectRow(project) {
           // Tentukan kelas CSS berdasarkan status
            let statusClass;
            if (project.status === 'Done') {
                statusClass = 'done';
            } else if (project.status === 'In Progress') {
                statusClass = 'in-progress';
            } else {
                statusClass = 'draft';
            }
            
            // Return HTML string untuk Project Card
            return `
                <div id="project-${project.id}" class="card shadow mb-4 card-project ${statusClass}">
                    <div class="card-body">
                        <div onclick="editProject(${project.id}, '${project.name}')" style="cursor: pointer;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">${project.name}</h4>
                                <button onclick="showAddTaskModal(${project.id}, '${project.name}'); event.stopPropagation();"  class="btn btn-lg text-success p-0 pe-3 border-0" title="Add Task"     onmouseover="this.style.transform='scale(1.1)'; this.style.color='#157347'" 
                                    onmouseout="this.style.transform='scale(1)'; this.style.color='#198754'">
                                
                                    <i class="bi bi-plus-circle fs-3"></i>
                                </button>
                            </div>
                            
                            <p class="card-text text-primary fw-semibold mb-2">Status: <span class="badge bg-secondary">Draft</span></p>
                            
                            <div class="progress mb-3" role="progressbar" aria-label="Project Progress" aria-valuenow="${project.completion_progress}" aria-valuemin="0" aria-valuemax="100" style="height: 20px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: ${project.completion_progress}%">
                                    ${project.completion_progress}%
                                </div>
                            </div>
                        </div>

                        <div id="tasks-list-${project.id}" class="mt-3 border-top pt-3">
                            <p class="text-secondary fst-italic">Belum ada Tasks. Klik (+) untuk menambahkan.</p>
                        </div>
                        
                    </div>
                </div>
            `;
        }


        // ==========================================================
        // LOGIKA CRUD PROJECT
        // ==========================================================
        
        
        // Submit Form Add/Edit Project
        $('#projectForm').submit(function(e) {
            e.preventDefault();
            
            // Siapkan data form
            let formData = $(this).serialize();
            let mode = $('#project-mode').val();
            let projectId = $('#project-id').val();
            let url, method;

            // Tentukan URL & Method berdasarkan mode
            if (mode === 'create') {
                url = "{{ route('projects.store') }}";
                method = 'POST';
            } else {
                url = "{{ url('projects') }}/" + projectId;
                method = 'PUT';
            }

            // Kirim AJAX Request
            $.ajax({
                url: url,
                type: method,
                data: formData,
                dataType: 'json',
                success: function(response) {
                    // Tutup Modal
                    $('#projectModal').modal('hide');
                    
                    if (mode === 'create') {
                        // Tambah project baru ke variabel global dan DOM
                        ALL_PROJECTS[response.project.id] = response.project.name;                         
                        $('#projects-list').prepend(renderProjectRow(response.project)); 

                    } else {
                       // Perbarui nama project di variabel global dan DOM
                       ALL_PROJECTS[projectId] = response.project.name;                        
                       $('#project-' + projectId).find('.card-title').text(response.project.name);  
                       // Update juga nama project di daftar task jika ada                      
                       $('#tasks-list-' + projectId).find('li').each(function() {
                            const oldOnClick = $(this).find('div[onclick]').attr('onclick');
                            if (oldOnClick) {
                                const newOnClick = oldOnClick.replace(
                                    /'[^']*'\)$/, 
                                    `'${response.project.name}')` 
                                );
                                $(this).find('div[onclick]').attr('onclick', newOnClick);
                            }
                        });
                        
                        updateProjectView(response.project); 
                    }
                    console.log(response.message);
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    if (errors && errors.name) {
                         alert('Gagal: ' + errors.name.join(', '));
                    } else {
                        Swal.fire('Gagal!', errorMessage, 'error');
                    }
                }
            });
        });

        // Reset Modal Project
        window.resetProjectModal = function() {
            $('#projectForm')[0].reset();
            $('#projectModalLabel').text('Add Project');
            $('#project-mode').val('create');
            $('#project-id').val('');
            $('#project-name').val('');
            $('#btn-delete-project').hide(); 
        };
        
        // Edit Project 
        window.editProject = function(projectId, projectName) {
            // DAPATKAN NAMA PROJECT TERBARU DARI VARIABEL GLOBAL
            const updatedProjectName = ALL_PROJECTS[projectId] || currentProjectName; 

            // Siapkan Modal untuk Edit
            $('#projectModalLabel').text('Edit Project');
            $('#project-mode').val('edit');
            $('#project-id').val(projectId);
            
            //  Mengisi input field modal Project dengan nama yang terbaru.
            $('#project-name').val(updatedProjectName); 
            
            // Tampilkan tombol Hapus
            $('#btn-delete-project').show().attr('onclick', `deleteProject(${projectId})`);
            
            const projectModal = new bootstrap.Modal(document.getElementById('projectModal'));
            projectModal.show();
        };

        // Hapus Project
        window.deleteProject = function(projectId) {
            Swal.fire({
                title: 'Yakin Hapus Project?',
                text: "Project ini dan SEMUA Task di dalamnya akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('projects') }}/" + projectId,
                        type: 'DELETE',
                        success: function(response) {
                            // DOM: Hapus elemen Project Card
                            $('#project-' + response.project_id).fadeOut(300, function() {
                                $(this).remove(); 
                            });

                            // Hapus item dari objek JavaScript
                            if (ALL_PROJECTS[projectId]) {
                                delete ALL_PROJECTS[projectId]; // Hapus item dari objek JavaScript
                            }
                            
                            // Tutup Modal Project
                            $('#projectModal').modal('hide'); 
                            
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus Project.', 'error');
                        }
                    });
                }
            });
        };

        // Reset Modal Project saat ditutup
        $('#projectModal').on('hidden.bs.modal', function () {
            resetProjectModal();
        });

        // ==========================================================
        // LOGIKA CRUD TASK
        // ==========================================================
        
        // Reset Modal Task 
        window.resetTaskModal = function() {
            $('#taskForm')[0].reset();
            $('#taskModalLabel').text('Add Task Baru');
            $('#task-mode').val('create');
            $('#task-id').val('');
            $('#btn-delete-task').hide(); 
            $('#task-status').val('Draft'); 
            $('#task-weight').val(1);

            $('#task-project-name-display').hide();
            $('#task-project-select-field').hide(); 
            $('#task-project-id').val(''); 
        };
                
        // Membuka Modal Add Task (VIA CARD PROJECT '+')
        window.showAddTaskModal = function(projectId, currentProjectName) {
            resetTaskModal();
            
           const updatedProjectName = ALL_PROJECTS[projectId] || currentProjectName;
            
            $('#task-project-name-display').show();
            $('#task-project-name').text(updatedProjectName);
            $('#task-project-id').val(projectId);
                    
            const taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
            taskModal.show();
        };


        // Membuka Modal Add Task (VIA GLOBAL BUTTON)
        window.showAddTaskModalGlobal = function() {
            resetTaskModal();
            
            $('#taskModalLabel').text('Add Task');

            $('#task-project-select-field').show();
            
            let selectHtml = '<option value="">-- Pilih Project --</option>';
            for (const id in ALL_PROJECTS) {
                selectHtml += `<option value="${id}">${ALL_PROJECTS[id]}</option>`;
            }
            $('#task-project-select').html(selectHtml);
            
            const taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
            taskModal.show();
        };
        
        // Membuka Modal Edit Task 
        window.editTask = function(taskId, projectId, taskName, taskStatus, taskWeight, projectName) {
            resetTaskModal(); 
                    
            const updatedProjectName = ALL_PROJECTS[projectId] || projectName; 

            $('#taskModalLabel').text('Edit Task');
            $('#task-mode').val('edit');
            $('#task-id').val(taskId);
            
            // Tampilkan Nama Project Statis dri nama yang terbaru
            $('#task-project-name-display').show();
            $('#task-project-name').text(updatedProjectName);
            $('#task-project-id').val(projectId);
            
            // Isi Data Task
            $('#task-name').val(taskName);
            $('#task-status').val(taskStatus);
            $('#task-weight').val(taskWeight);
            
            // Tampilkan Status dan Tombol Hapus saat Edit
            $('#task-status-field').show();
            $('#btn-delete-task').show().attr('onclick', `deleteTask(${taskId})`);
            
            const taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
            taskModal.show();
        };

        // Submit Form Task (Add/Edit)
        $('#taskForm').submit(function(e) {
            e.preventDefault();
            
            let mode = $('#task-mode').val();
            let taskId = $('#task-id').val();
            let url, method;
            let finalProjectId;

            // Tentukan Project ID berdasarkan mode input
            if ($('#task-project-name-display').is(':visible')) {
                // Mode Card atau Edit: ID diambil dari hidden field
                finalProjectId = $('#task-project-id').val();
            } else if ($('#task-project-select-field').is(':visible')) {
                // Mode Global: ID diambil dari dropdown select
                finalProjectId = $('#task-project-select').val();
            }
            
            // VALIDASI MANUAL JIKA ID Project Kosong
            if (!finalProjectId) {
                Swal.fire('Perhatian!', 'Mohon pilih Project induk terlebih dahulu.', 'warning'); 
                return; 
            }

            let formDataArray = $(this).serializeArray();
            
            formDataArray = formDataArray.filter(item => item.name !== 'project_id');
            
            formDataArray.push({ name: 'project_id', value: finalProjectId });

            // Tentukan URL & Method berdasarkan mode
            if (mode === 'create') {
                url = "{{ route('tasks.store') }}";
                method = 'POST';
            } else {
                url = "{{ url('tasks') }}/" + taskId;
                method = 'PUT';
            }

            // Kirim AJAX Request
            $.ajax({
                url: url,
                type: method,
                data: $.param(formDataArray), 
                dataType: 'json',
                success: function(response) {
                    $('#taskModal').modal('hide');
                    
                    let taskHtml = renderTaskRow(response.task);
                    let $tasksListContainer = $('#tasks-list-' + response.task.project_id);
                    
                    if (mode === 'create') {
                        if ($tasksListContainer.find('.fst-italic').length) {
                            $tasksListContainer.html('<p class="text-primary fw-semibold">Tasks:</p><ul class="list-group list-group-flush"></ul>');
                        }
                        $tasksListContainer.find('ul').append(taskHtml);
                    } else {
                        $('#task-' + taskId).replaceWith(taskHtml); 
                    }
                    
                    updateProjectView(response.project);
                    console.log(response.message);
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON && xhr.responseJSON.errors ? Object.values(xhr.responseJSON.errors).flat().join('\n') : 'Terjadi kesalahan server.';
                    Swal.fire('Gagal Menyimpan Task!', errors, 'error');
                    console.error('AJAX Error:', xhr);
                }
            });
        });

        // Hapus Task
        window.deleteTask = function(taskId) {
            Swal.fire({
                title: 'Yakin Hapus Task?',
                text: "Task ini akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('tasks') }}/" + taskId,
                        type: 'DELETE',
                        success: function(response) {
                            // Hapus elemen Task dari DOM
                            $('#task-' + taskId).remove();
                            
                            // Update Progress Project
                            updateProjectView(response.project);
                            
                            // Cek jika Task sudah habis, tampilkan placeholder
                            let $tasksListContainer = $('#tasks-list-' + response.project.id);
                            if ($tasksListContainer.find('li').length === 0) {
                                $tasksListContainer.html('<p class="text-secondary fst-italic">Belum ada Tasks. Klik (+) untuk menambahkan.</p>');
                            }
                            
                            // Tutup Modal Task
                            $('#taskModal').modal('hide'); 

                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus Task.', 'error');
                        }
                    });
                }
            });
        };
        
        // Reset Modal Task saat ditutup
        $('#taskModal').on('hidden.bs.modal', function () {
            resetTaskModal();
        });

    </script>
</body>
</html>