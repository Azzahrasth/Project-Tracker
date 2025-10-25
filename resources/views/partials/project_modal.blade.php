<div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white ">
                <h5 class="modal-title" id="projectModalLabel">Add Project</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="projectForm">
                <div class="modal-body">
                    @csrf                    
                    <input type="hidden" id="project-id" name="id">
                    <input type="hidden" id="project-mode" value="create"> 
                    
                    <div class="mb-3">
                        <label for="project-name" class="form-label">Nama Project:</label>
                        <input type="text" class="form-control" id="project-name" name="name" placeholder="Contoh: Project HRIS" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" id="btn-delete-project" class="btn btn-danger me-auto" style="display: none;">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-save-project">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>