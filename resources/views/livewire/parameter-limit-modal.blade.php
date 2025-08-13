<div>
    <div class="modal fade" id="parameterLimitModal" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self
        data-bs-backdrop="static" data-bs-keyboard="false">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Parameter Limits</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="closeModal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="parameter-name" class="form-label">Parameter</label>
                        <input type="text" class="form-control" id="parameter-name" wire:model="parameterName"
                            readonly>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="warning-limit" class="form-label">Warning Limit</label>
                            <div class="input-group">
                                <input type="number" step="0.01"
                                    class="form-control @error('warningLimit') is-invalid @enderror" id="warning-limit"
                                    wire:model.defer="warningLimit" placeholder="Set warning limit">
                                <span class="input-group-text">{{ $unit ?? '' }}</span>
                            </div>
                            @error('warningLimit')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="danger-limit" class="form-label">Danger Limit</label>
                            <div class="input-group">
                                <input type="number" step="0.01"
                                    class="form-control @error('dangerLimit') is-invalid @enderror" id="danger-limit"
                                    wire:model.defer="dangerLimit" placeholder="Set danger limit">
                                <span class="input-group-text">{{ $unit ?? '' }}</span>
                            </div>
                            @error('dangerLimit')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="updateLimits"
                        wire:loading.attr="disabled">
                        <span wire:loading wire:target="updateLimits" class="spinner-border spinner-border-sm me-1"
                            role="status" aria-hidden="true"></span>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle modal events safely
                const modalEl = document.getElementById('parameterLimitModal');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);

                    // Listen for Livewire events - only register these on initial load
                    if (!window.modalEventsRegistered) {
                        window.modalEventsRegistered = true;

                        Livewire.on('openParameterLimitModal', () => {
                            modal.show();
                        });

                        Livewire.on('modalClosed', () => {
                            modal.hide();
                        });
                    }

                    // Handle modal hidden event to ensure Livewire state is updated
                    modalEl.addEventListener('hidden.bs.modal', function() {
                        Livewire.dispatch('closeModal');
                    });
                }

                // Remove the toast notification handler from here as it's now in index.blade.php
            });
        </script>
    @endpush
</div>
