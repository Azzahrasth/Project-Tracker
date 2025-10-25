<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="taskModalLabel">Add Task Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="taskForm">
                <div class="modal-body">
                    @csrf
                    
                    {{-- Hidden Fields --}}
                    <input type="hidden" id="task-project-id" name="project_id">
                    <input type="hidden" id="task-id" name="id">
                    <input type="hidden" id="task-mode" value="create"> 
                    
                    {{-- FIELD PROJECT (DISPLAY NAMA) --}}
                    <div class="mb-3" id="task-project-name-display">
                        <label class="form-label">Project:</label>
                        <p class="form-control-plaintext border-bottom pb-2 fw-bold" id="task-project-name">Nama Project Induk</p>
                    </div>

                    {{-- DROPDOWN PEMILIH PROJECT (MODE GLOBAL) --}}
                    <div class="mb-3" id="task-project-select-field" style="display: none;">
                        <label for="task-project-select" class="form-label">Project:</label>
                        <select class="form-select" id="task-project-select" name="project_id">
                            {{-- Options diisi JS --}}
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="task-name" class="form-label">Nama Task:</label>
                        <input type="text" class="form-control" id="task-name" name="name" placeholder="Contoh: Desain Database" required>
                    </div>

                    <div class="mb-3" id="task-status-field" style="display: block;"> 
                        <label for="task-status" class="form-label">Status:</label>
                        <select class="form-select" id="task-status" name="status">
                            <option value="Draft">Draft</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Done">Done</option>
                        </select>
                        <small class="text-info">Perubahan status Task akan mempengaruhi status Project.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="task-weight" class="form-label">Bobot:</label>
                        <input type="number" class="form-control" id="task-weight" name="weight" min="1" required value="1">
                        <small class="text-muted">Bobot Task menentukan kontribusinya pada Completion Progress Project.</small>
                    </div>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" id="btn-delete-task" class="btn btn-danger me-auto" style="display: none;">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                    <button type="submit" class="btn btn-success " id="btn-save-task">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>