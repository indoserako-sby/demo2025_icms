<div>
    <style>
        /* ApexCharts Tooltip Styling - Transparent Glass Effect */
        .apexcharts-tooltip {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(15px) !important;
            -webkit-backdrop-filter: blur(15px) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
            color: #000 !important;
        }

        .apexcharts-tooltip-title {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(15px) !important;
            -webkit-backdrop-filter: blur(15px) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 8px 8px 0 0 !important;
            color: #000 !important;
            font-weight: 600 !important;
            padding: 8px 12px !important;
        }

        /* Styling untuk konten tooltip */
        .apexcharts-tooltip-y-group {
            background: transparent !important;
        }

        .apexcharts-tooltip-text-y-label,
        .apexcharts-tooltip-text-y-value {
            background: transparent !important;
            color: #000 !important;
        }

        .apexcharts-tooltip-series-group {
            background: transparent !important;
        }

        /* Force tooltip to always appear above the data point */
        .apexcharts-tooltip {
            transform: translateY(-100%) !important;
        }

        .apexcharts-tooltip.apexcharts-theme-light {
            transform: translateY(-100%) !important;
        }

        /* Remove default accordion arrow */
        .accordion-button::after {
            display: none !important;
        }

        .sidebar-collapsed {
            overflow: hidden;
        }

        .sidebar-collapsed .card {
            display: none;
        }

        .sidebar-collapsed-toggle {
            position: fixed;
            /* Fixed positioning so it doesn't move on scroll */
            top: 90px;
            /* Match the position of the card */
            left: 0;
            /* Align to the left edge */
            z-index: 100;
            height: auto;
            display: flex;
            justify-content: flex-start;
            padding: 15px 0;
            margin-left: 0;
        }

        /* Add specific styling for the expand button in collapsed mode */
        .sidebar-collapsed-toggle .btn {
            margin-left: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            width: 42px;
            height: 42px;
        }

        .transition-width {
            transition: width 0.3s ease;
        }
    </style>

    <div class="row">
        <!-- Left Column - Selection Panel with Tree Structure -->
        <div id="cross-asset-sidebar" class="col-md-3 transition-width">
            <!-- Toggle button for collapsed state -->
            <div class="sidebar-collapsed-toggle d-none">
                <button id="expand-cross-sidebar" class="btn btn-primary" title="Show Asset Panel">
                    <i class="ti ti-chevron-right"></i>
                </button>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Cross Asset Analysis</h5>
                        <!-- Add toggle button inside the card -->
                        <button id="collapse-cross-sidebar" class="btn btn-primary" title="Hide Asset Panel">
                            <i class="ti ti-chevron-left"></i>
                        </button>
                    </div>

                    <!-- Tree Structure for Selection -->
                    {{-- <livewire:simple-tree-panel /> --}}
                    <livewire:selection-tree-panel :selected-parameters="$selectedParameters" />
                </div>
            </div>
        </div>

        <!-- Right Column - Chart Display -->
        <div id="cross-asset-content" class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">{{ $chartTitle }}</h5>
                        <div class="d-flex align-items-center gap-3">
                            <select class="form-select" id="cross-asset-interval-select" style="width: auto;">
                                <option value="raw">Raw Data</option>
                                <option value="3-minutes">3 Minutes</option>
                                <option value="10-minutes">10 Minutes</option>
                                <option value="15-minutes">15 Minutes</option>
                                <option value="30-minutes">30 Minutes</option>
                                <option value="hour">1 Hour</option>
                                <option value="4-hours">4 Hours</option>
                                <option value="6-hours">6 Hours</option>
                                <option value="12-hours">12 Hours</option>
                                <option value="day">1 Day</option>
                            </select>

                            <div style="min-width: 250px;">
                                <input type="text" class="form-control bs-daterangepicker-range"
                                    id="cross-asset-date-range" placeholder="Select date range" wire:model="dateRange">
                            </div>
                            <button id="export-cross-asset-data" class="btn btn-primary form-control">
                                <i class="ti ti-file-export me-1"></i> Export Log Data
                            </button>
                        </div>
                    </div>

                    <!-- Chart Container -->
                    <div id="crossAssetChartContainer" class="position-relative" style="min-height: 600px;">
                        <div id="crossAssetChart" style="width: 100%; height: 600px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let crossAssetChart;
                let currentParameters = [];
                let chartData = null;
                let isTreeOperation = false;

                // Define isRtl variable based on document direction
                let isRtl = document.querySelector('html').getAttribute('dir') === 'rtl';

                // Toggle sidebar functionality
                const assetSidebar = document.getElementById('cross-asset-sidebar');
                const assetContent = document.getElementById('cross-asset-content');
                const collapseSidebarBtn = document.getElementById('collapse-cross-sidebar');
                const expandSidebarBtn = document.getElementById('expand-cross-sidebar');
                const collapsedToggle = assetSidebar.querySelector('.sidebar-collapsed-toggle');
                let sidebarVisible = true;

                // Collapse sidebar function
                collapseSidebarBtn.addEventListener('click', function() {
                    // Collapse sidebar
                    assetSidebar.classList.add('sidebar-collapsed');
                    assetSidebar.classList.remove('col-md-3');
                    assetSidebar.classList.add('col-md-1');
                    collapsedToggle.classList.remove('d-none');

                    // Expand content
                    assetContent.classList.remove('col-md-9');
                    assetContent.classList.add('col-md-11');

                    sidebarVisible = false;

                    // Trigger resize event to make sure chart redraws correctly
                    if (crossAssetChart) {
                        setTimeout(function() {
                            window.dispatchEvent(new Event('resize'));
                            crossAssetChart.render();
                        }, 300);
                    }
                });

                // Expand sidebar function
                expandSidebarBtn.addEventListener('click', function() {
                    // Expand sidebar
                    assetSidebar.classList.remove('sidebar-collapsed');
                    assetSidebar.classList.remove('col-md-1');
                    assetSidebar.classList.add('col-md-3');
                    collapsedToggle.classList.add('d-none');

                    // Reduce content width
                    assetContent.classList.remove('col-md-11');
                    assetContent.classList.add('col-md-9');

                    sidebarVisible = true;

                    // Sync checkboxes with currentParameters after sidebar is expanded
                    // to ensure UI is consistent with the data model
                    setTimeout(function() {
                        if (currentParameters && currentParameters.length > 0) {
                            // First uncheck all checkboxes
                            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                                checkbox.checked = false;
                            });

                            // Then check only those that are in currentParameters
                            currentParameters.forEach(paramId => {
                                const checkbox = document.getElementById('param-' + paramId);
                                if (checkbox) {
                                    checkbox.checked = true;
                                }
                            });
                        }
                    }, 100);

                    // Trigger resize event to make sure chart redraws correctly
                    if (crossAssetChart) {
                        setTimeout(function() {
                            window.dispatchEvent(new Event('resize'));
                            crossAssetChart.render();
                        }, 300);
                    }
                });

                // Add event listener for interval dropdown
                document.getElementById('cross-asset-interval-select').addEventListener('change', function() {
                    if (dateRangePicker) {
                        const startDate = dateRangePicker.startDate.format('YYYY-MM-DD');
                        const endDate = dateRangePicker.endDate.format('YYYY-MM-DD');
                        updateChartWithNewData(startDate, endDate);
                    }
                });

                // Initialize date range picker
                const bsRangePickerRange = $('#cross-asset-date-range');
                bsRangePickerRange.daterangepicker({
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')]
                    },
                    startDate: moment(@js($startDate)),
                    endDate: moment(@js($endDate)),
                    opens: isRtl ? 'left' : 'right'
                }, function(start, end, label) {
                    if (currentParameters.length > 0) {
                        const startDate = start.format('YYYY-MM-DD');
                        const endDate = end.format('YYYY-MM-DD');
                        updateChartWithNewData(startDate, endDate);
                    }
                });

                // Adding btn-secondary class in cancel btn
                const bsRangePickerCancelBtn = document.getElementsByClassName('cancelBtn');
                for (var i = 0; i < bsRangePickerCancelBtn.length; i++) {
                    bsRangePickerCancelBtn[i].classList.remove('btn-default');
                    bsRangePickerCancelBtn[i].classList.add('btn-secondary');
                }

                // Store reference to the daterangepicker instance
                const dateRangePicker = bsRangePickerRange.data('daterangepicker');

                // Function to update chart via AJAX
                function updateChartWithNewData(startDate, endDate) {
                    if (isTreeOperation) {
                        console.log('Skipping chart update during tree operation');
                        return;
                    }

                    console.log('Updating chart with parameters:', currentParameters);

                    const tz = 'Asia/Jakarta';
                    const formatDate = d => new Date(d).toLocaleString('sv-SE', {
                        timeZone: tz
                    }).replace(' ', 'T');
                    const formattedStartDate = formatDate(startDate);
                    const formattedEndDate = formatDate(endDate);

                    fetch('/update-cross-chart-parameters', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                parameters: currentParameters,
                                start_date: formattedStartDate,
                                end_date: formattedEndDate,
                                interval: document.getElementById('cross-asset-interval-select').value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.chartData) {
                                chartData = data.chartData;
                                updateChartDisplay(data.chartData);

                                // Update interval dropdown if specified in response
                                if (data.interval) {
                                    const intervalDropdown = document.getElementById('cross-asset-interval-select');
                                    if (intervalDropdown && intervalDropdown.value !== data.interval) {
                                        intervalDropdown.value = data.interval;
                                    }
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error updating chart:', error);
                        });
                }



                // Initialize chart with default options
                const options = {
                    series: [],
                    chart: {
                        height: 600,
                        type: 'line',
                        zoom: {
                            enabled: true,
                            type: 'x'
                        },
                        animations: {
                            enabled: true
                        },
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: true,
                                zoom: true,
                                zoomin: true,
                                zoomout: true,
                                pan: true,
                                reset: true
                            }
                        }
                    },
                    stroke: {
                        width: 3,
                        curve: 'smooth',
                        lineCap: 'round'
                    },
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            datetimeUTC: false,
                            format: 'yyyy-MM-dd HH:mm'
                        },
                        title: {
                            text: 'Date'
                        }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        x: {
                            format: 'yyyy-MM-dd HH:mm'
                        },
                        y: {
                            formatter: function(value, {
                                series,
                                seriesIndex
                            }) {
                                let unit = '';
                                if (chartData && chartData.yAxis && chartData.yAxis[seriesIndex]) {
                                    unit = chartData.yAxis[seriesIndex].unit || '';
                                }
                                return value.toFixed(2) + (unit ? ' ' + unit : '');
                            }
                        }
                    }
                };

                // Create initial empty chart
                crossAssetChart = new ApexCharts(document.querySelector("#crossAssetChart"), options);
                crossAssetChart.render();

                // Function to update chart display
                function updateChartDisplay(chartData) {
                    if (isTreeOperation) {
                        console.log('Skipping chart update during tree operation');
                        return;
                    }

                    if (!chartData || !Array.isArray(chartData.series)) {
                        console.warn('Invalid chart data received');
                        return;
                    }

                    console.log('Updating chart display with data:', chartData);

                    // Process series data
                    const series = chartData.series.map(s => ({
                        name: s.name,
                        data: Array.isArray(s.data) ? s.data.map(point => ({
                            x: new Date(point[0]).getTime(),
                            y: point[1]
                        })) : []
                    }));

                    // Configure Y-axes with proper configuration for each series
                    const yAxis = chartData.yAxis.map((axis, index) => {
                        const seriesData = series[index]?.data || [];
                        let values = seriesData.map(point => point.y).filter(val => !isNaN(val));
                        let minValue = Math.min(...values);
                        let maxValue = Math.max(...values);

                        // Add padding (10% of the range)
                        let range = maxValue - minValue;
                        let padding = range * 0.1;

                        // Determine axis position based on index, not visibility
                        const isRightSide = index % 2 !== 0 && index > 0;

                        // Get annotations for single parameter case
                        const annotations = [];
                        if (series.length === 1 && axis.annotations) {
                            axis.annotations.forEach(annotation => {
                                annotations.push({
                                    y: annotation.y,
                                    borderColor: annotation.borderColor,
                                    strokeDashArray: 0,
                                    label: {
                                        text: annotation.label.text,
                                        position: 'right',
                                        offsetX: 5,
                                        style: {
                                            color: annotation.label.style.color,
                                            background: annotation.label.style.background,
                                            padding: {
                                                left: 5,
                                                right: 5,
                                                top: 2,
                                                bottom: 2
                                            },
                                            borderRadius: 3
                                        }
                                    }
                                });
                            });
                        }

                        return {
                            seriesName: series[index]?.name,
                            title: {
                                text: axis.name + (axis.unit ? ` (${axis.unit})` : ''),
                                style: {
                                    fontSize: '12px'
                                }
                            },
                            tickAmount: 5,
                            min: Math.floor(minValue - padding),
                            max: Math.ceil(maxValue + padding),
                            opposite: isRightSide,
                            decimalsInFloat: 2,
                            showAlways: true,
                            labels: {
                                formatter: function(value) {
                                    return value.toFixed(2);
                                },
                                style: {
                                    fontSize: '11px'
                                },
                                align: isRightSide ? 'left' : 'right'
                            },
                            annotations: annotations // Add annotations to y-axis
                        };
                    });

                    // Update chart options and redraw
                    crossAssetChart.updateOptions({
                        series: series,
                        title: {
                            text: chartData.title || 'Cross Asset Analysis',
                            align: 'center'
                        },
                        yaxis: yAxis,
                        annotations: {
                            position: 'front',
                            yaxis: yAxis.reduce((acc, axis) => [...acc, ...(axis.annotations || [])], [])
                        }
                    }, true, true);
                }

                // Listen for parameter selection changes
                Livewire.on('parameterSelectionChanged', (parameters) => {
                    if (!isTreeOperation) {
                        currentParameters = parameters;
                        if (dateRangePicker) {
                            const startDate = dateRangePicker.startDate.format('YYYY-MM-DD');
                            const endDate = dateRangePicker.endDate.format('YYYY-MM-DD');
                            updateChartWithNewData(startDate, endDate);
                        }
                    }
                });

                // Listen for chart update events from Livewire
                Livewire.on('updateChart', data => {
                    if (!data) {
                        console.warn('No chart data received');
                        return;
                    }
                    if (!isTreeOperation) {
                        chartData = data;
                        updateChartDisplay(data);
                    }
                });

                // Export functionality
                const exportButton = document.getElementById('export-cross-asset-data');
                if (exportButton) {
                    exportButton.addEventListener('click', function() {
                        // Show loading state
                        exportButton.disabled = true;
                        exportButton.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i> Exporting...';

                        // Get selected parameters
                        if (currentParameters.length === 0) {
                            alert('Please select at least one parameter to export data.');
                            exportButton.disabled = false;
                            exportButton.innerHTML = '<i class="ti ti-file-export me-1"></i> Export Log Data';
                            return;
                        }

                        // Get current date range
                        const dateRangePicker = $('.bs-daterangepicker-range').data('daterangepicker');
                        let startDate, endDate;

                        if (dateRangePicker) {
                            startDate = dateRangePicker.startDate.format('YYYY-MM-DD');
                            endDate = dateRangePicker.endDate.format('YYYY-MM-DD');
                        } else {
                            // Use default date range if picker not available
                            const now = new Date();
                            endDate = now.toISOString().split('T')[0]; // Today
                            startDate = new Date(now.setDate(now.getDate() - 7)).toISOString().split('T')[
                                0]; // Last 7 days
                        }

                        // Create a form to submit the export request
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('export-cross-asset-log-data') }}';
                        form.style.display = 'none';

                        // Create CSRF token input
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content');
                        form.appendChild(csrfToken);

                        // Add interval dropdown to export button area
                        const intervalInput = document.createElement('input');
                        intervalInput.type = 'hidden';
                        intervalInput.name = 'interval';
                        intervalInput.value = document.getElementById('cross-asset-interval-select')?.value ||
                            'raw';
                        form.appendChild(intervalInput);

                        // Create parameters input (as multiple inputs for array)
                        // Use the currentParameters array which is maintained by Livewire
                        // This ensures parameters are available even when sidebar is collapsed
                        let paramIds = [];

                        // First try to use currentParameters which is maintained across sidebar states
                        if (currentParameters && currentParameters.length > 0) {
                            paramIds = Array.from(currentParameters);


                        } else {
                            // Fallback to checking DOM for checkboxes (when sidebar is visible)
                            const paramsinput = document.querySelectorAll('input[type="checkbox"]:checked');
                            if (paramsinput.length > 0) {
                                paramIds = Array.from(paramsinput).map(input => parseInt(input.value));
                            } else {
                                alert('Please select at least one parameter to export data.');
                                exportButton.disabled = false;
                                exportButton.innerHTML =
                                    '<i class="ti ti-file-export me-1"></i> Export Log Data';
                                return;
                            }
                        }

                        // Add each parameter ID as a separate input field with the correct name format
                        paramIds.forEach(function(paramId, index) {
                            const paramField = document.createElement('input');
                            paramField.type = 'hidden';
                            paramField.name = 'parameters[' + index + ']';
                            paramField.value = paramId; // Use the individual parameter ID
                            form.appendChild(paramField);
                        });

                        // Create start_date input
                        const startDateField = document.createElement('input');
                        startDateField.type = 'hidden';
                        startDateField.name = 'start_date';
                        startDateField.value = startDate;
                        form.appendChild(startDateField);

                        // Create end_date input
                        const endDateField = document.createElement('input');
                        endDateField.type = 'hidden';
                        endDateField.name = 'end_date';
                        endDateField.value = endDate;
                        form.appendChild(endDateField);

                        // Append form to body
                        document.body.appendChild(form);

                        // Add event listener to reset button state after form submission
                        window.addEventListener('focus', function onFocus() {
                            setTimeout(function() {
                                exportButton.disabled = false;
                                exportButton.innerHTML =
                                    '<i class="ti ti-file-export me-1"></i> Export Log Data';
                            }, 1000);
                            window.removeEventListener('focus', onFocus);
                        });

                        // Submit the form to trigger file download
                        form.submit();

                        // Remove form from document
                        setTimeout(function() {
                            document.body.removeChild(form);
                        }, 5000);
                    });
                }
            });
        </script>
    @endpush
</div>
