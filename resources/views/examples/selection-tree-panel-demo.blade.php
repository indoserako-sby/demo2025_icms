@extends('dashboard')

@section('title', 'Selection Tree Panel Demo')

@section('content')
    <div class="row">
        <!-- Left Column - Selection Tree Panel -->
        <div class="col-md-3">
            <livewire:selection-tree-panel />
        </div>

        <!-- Right Column - Content Area -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Selected Parameters</h5>
                    <div id="selectedParametersDisplay" class="alert alert-info">
                        No parameters selected yet.
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for parameter selection changes from the Livewire component
            Livewire.on('parameterSelectionChanged', (selectedParameters) => {
                const display = document.getElementById('selectedParametersDisplay');

                if (selectedParameters && selectedParameters.length > 0) {
                    display.innerHTML = `
                    <h6>Selected Parameters (${selectedParameters.length}):</h6>
                    <ul>
                        ${selectedParameters.map(param => `<li>Parameter ID: ${param}</li>`).join('')}
                    </ul>
                    <p>You can now process these parameter IDs in your application.</p>
                `;
                    display.classList.remove('alert-info');
                    display.classList.add('alert-success');
                } else {
                    display.innerHTML = 'No parameters selected yet.';
                    display.classList.remove('alert-success');
                    display.classList.add('alert-info');
                }
            });
        });
    </script>
@endpush
