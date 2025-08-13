<div>
    <div class="mb-4">
        <label for="area">Pilih Area:</label>
        <select id="area" wire:model.live="selectedArea" class="form-control select2">
            <option value="">-- Pilih Area --</option>
            @foreach ($areas as $area)
                <option value="{{ $area->id }}">{{ $area->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="group">Group Terkait:</label>
        <select id="group" wire:model.live="selectedGroup" class="form-control select2">
            <option value="">-- Pilih Group --</option>
            @foreach ($groups as $group)
                <option value="{{ $group->id }}">{{ $group->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="asset">Asset:</label>
        <select id="asset" wire:model.live="selectedAsset" class="form-control select2">
            <option value="">-- Pilih Asset --</option>
            @foreach ($assets as $asset)
                <option value="{{ $asset->id }}">{{ $asset->name }}</option>
            @endforeach
        </select>
    </div>
</div>

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
        function initSelect2() {
            // Destroy terlebih dahulu jika sudah terinisialisasi
            $('.select2').each(function() {
                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }
            });

            // Inisialisasi baru
            $('.select2').select2({
                placeholder: "-- Silahkan Pilih --",
                allowClear: true,
                width: '100%'
            });

            // Bind event handlers setelah inisialisasi Select2
            $('#area').on('change.select2', function(e) {
                var data = $(this).val();
                @this.set('selectedArea', data);
            });

            $('#group').on('change.select2', function(e) {
                var data = $(this).val();
                @this.set('selectedGroup', data);
                // Reinisialisasi Select2 setelah memilih group
                setTimeout(() => initSelect2(), 50);
            });

            $('#asset').on('change.select2', function(e) {
                var data = $(this).val();
                @this.set('selectedAsset', data);
                // Reinisialisasi Select2 setelah memilih asset
                setTimeout(() => initSelect2(), 50);
            });

            // Tidak ada pembatasan disabled lagi
            // Semua select box dapat dipilih kapan saja
        }

        // Inisialisasi pertama kali halaman dimuat
        // document.addEventListener('DOMContentLoaded', function() {
        //     initSelect2();
        // });

        // Untuk Livewire 3.x
        document.addEventListener('livewire:initialized', function() {
            // Event ketika Livewire telah diinisialisasi
            initSelect2();

            // Untuk Livewire 3.x, gunakan on untuk event kustom
            @this.on('updatedGroups', () => {
                setTimeout(function() {
                    initSelect2();
                }, 100);
            });
        });

        // Perbarui setiap kali komponen Livewire selesai me-render ulang
        document.addEventListener('livewire:navigating', function() {
            initSelect2();
        });

        // DOMContentLoaded yang telah terbukti bekerja dengan baik
        document.addEventListener('DOMContentLoaded', function() {
            initSelect2();
        });

        // Hook tambahan untuk memastikan Select2 terinisialisasi setelah update apapun
        document.addEventListener('livewire:initialized', function() {
            Livewire.hook('element.updated', (el, component) => {
                if (el.querySelector('.select2') || el.classList.contains('select2')) {
                    setTimeout(() => initSelect2(), 50);
                }
            });

            // Hook for message processing - penting untuk reinisialisasi Select2
            Livewire.hook('message.processed', (message, component) => {
                setTimeout(() => initSelect2(), 50);
            });

            // Tambahan untuk memastikan event handling setelah update komponen
            window.addEventListener('updatedGroups', () => {
                setTimeout(() => initSelect2(), 50);
            });
        });
    </script>
@endpush
