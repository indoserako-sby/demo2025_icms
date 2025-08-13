<div>
    @if ($showModal)
        <div class="modal show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Acknowledge Alarm</h5>
                        <button type="button" class="btn-close" wire:click="close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Alarm Cause</label>
                            <select wire:model="alarmCause" class="form-select">
                                <option value="">Select cause...</option>
                                <option value="Fake Alarm">Fake Alarm</option>
                                <option value="Mall Function">Mall Function</option>
                                <option value="Test Alarm">Test Alarm</option>
                                <option value="Error Alarm">Error Alarm</option>
                            </select>
                            @error('alarmCause')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Machine Person</label>
                            <input type="text" wire:model="machine_person" class="form-control"
                                placeholder="Enter name of person">
                            @error('machine_person')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3" x-data="{ picker: null }" x-init="picker = flatpickr($refs.startDatePicker, {
                            enableTime: true,
                            dateFormat: 'Y-m-d H:i',
                            allowInput: true,
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                $wire.set('starttimemaintenance', dateStr);
                            }
                        })">
                            <label class="form-label">Start Time Maintenance</label>
                            <input type="text" wire:model="starttimemaintenance" x-ref="startDatePicker"
                                class="form-control flatpickr-input" placeholder="YYYY-MM-DD HH:MM" readonly>
                            @error('starttimemaintenance')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3" x-data="{ picker: null }" x-init="picker = flatpickr($refs.endDatePicker, {
                            enableTime: true,
                            dateFormat: 'Y-m-d H:i',
                            allowInput: true,
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                $wire.set('endtimemaintenance', dateStr);
                            }
                        })">
                            <label class="form-label">End Time Maintenance</label>
                            <input type="text" wire:model="endtimemaintenance" x-ref="endDatePicker"
                                class="form-control flatpickr-input" placeholder="YYYY-MM-DD HH:MM" readonly>
                            @error('endtimemaintenance')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea wire:model="notes" class="form-control" rows="3"></textarea>
                            @error('notes')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="close">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="acknowledgeAlarm">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>

@push('scripts')
    <script>
        window.addEventListener('livewire:load', function() {
            Livewire.on('closeModal', function() {
                document.querySelector('.modal-backdrop')?.remove();
            });
        });

        document.addEventListener('livewire:navigating', () => {
            // Cleanup flatpickr instances when navigating away
            const flatpickrElements = document.querySelectorAll('.flatpickr-input');
            flatpickrElements.forEach(element => {
                if (element._flatpickr) {
                    element._flatpickr.destroy();
                }
            });
        });
    </script>
@endpush
