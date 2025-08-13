<!-- Filters -->
<div class="mb-4 row g-3 align-items-end">
    <div class="col-md-2">
        <select wire:model.live="area" id="area" class="form-select">
            <option value="">Select Area</option>
            @foreach ($areas ?? [] as $areaOption)
                <option value="{{ $areaOption }}">{{ $areaOption }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select wire:model.live="group" id="group" class="form-select">
            <option value="">Select Group</option>
            @foreach ($groups ?? [] as $groupOption)
                <option value="{{ $groupOption }}">{{ $groupOption }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select wire:model.live="asset" id="asset" class="form-select">
            <option value="">Select Asset</option>
            @foreach ($assets ?? [] as $assetOption)
                <option value="{{ $assetOption }}">{{ $assetOption }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select wire:model.live="parameter" id="parameter" class="form-select">
            <option value="">Select Parameter</option>
            @foreach ($parameters ?? [] as $parameterOption)
                <option value="{{ $parameterOption['id'] }}">{{ $parameterOption['name'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-1">
        <select wire:model.live="alertType" id="alertType" class="form-select">
            <option value="">Select Alert Type</option>
            @foreach ($alertTypes ?? [] as $alertTypeOption)
                <option value="{{ $alertTypeOption }}">{{ ucfirst($alertTypeOption) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <input type="text" wire:model.live="dateRange" class="form-control flatpickr-input"
            placeholder="Select date range" id="flatpickr-range" />
    </div>
    <div class="col-md-1">
        <button wire:click="resetFilters" class="btn btn-secondary w-100">Reset</button>
    </div>
</div>
@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize flatpickr for date range selection
            flatpickr("#flatpickr-range", {
                mode: "range",
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    @this.set('dateRange', dateStr);
                }
            });
        });
    </script>
@endpush
