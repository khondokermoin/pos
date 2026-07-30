<div class="modal fade" id="extendModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="extendForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Extend Subscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Company:</strong> <span id="extendCompanyName"></span></p>
                    <p><strong>Current End Date:</strong> <span id="extendCurrentDate"></span></p>
                    <div class="mb-3">
                        <label class="form-label">Extend by (days) <span class="text-danger">*</span></label>
                        <input type="number" name="extend_days" class="form-control" min="1" max="365" value="30" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Extend Now</button>
                </div>
            </div>
        </form>
    </div>
</div>