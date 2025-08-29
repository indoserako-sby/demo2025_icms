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

        /* Asset Panel Customizer Style */
        .asset-panel {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background: #fff;
            border-left: 1px solid #e0e0e0;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1100;
            transition: right 0.3s ease;
            overflow-y: auto;
        }

        .asset-panel.active {
            right: 0;
        }

        .asset-panel-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
            background: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .asset-panel-body {
            padding: 1.5rem;
        }

        .panel-toggle-btn {
            position: fixed;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            z-index: 1060;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: none;
            background: #696cff;
            color: white;
            transition: all 0.3s ease;
        }

        .panel-toggle-btn:hover {
            background: #5f61e6;
            transform: translateY(-50%) scale(1.1);
        }

        .panel-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .panel-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .asset-panel {
                width: 100%;
                right: -100%;
            }

            .panel-toggle-btn {
                right: 15px;
                width: 45px;
                height: 45px;
            }
        }

        .transition-width {
            transition: width 0.3s ease;
        }
    </style>

    {{-- panel toggle  --}}
    <button class="panel-toggle-btn" id="panel-toggle" title="Asset Panel">
        <i class="ti ti-device-desktop"></i>
    </button>

    <!-- Panel Backdrop -->
    <div class="panel-backdrop" id="panel-backdrop"></div>

    <!-- Asset Panel (Customizer Style) -->
    <div class="asset-panel" id="asset-panel">
        <div class="asset-panel-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Cross Asset Selection</h5>
                <button class="btn btn-sm btn-icon btn-outline-secondary" id="panel-close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        </div>
        <div class="asset-panel-body">
            <livewire:selection-tree-panel :selected-parameters="$selectedParameters" />
        </div>
    </div>

    <div class="row">
        <!-- Chart Display -->
        <div id="cross-asset-content" class="col-md-12">
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

                // Panel elements
                const panelToggle = document.getElementById('panel-toggle');
                const panelClose = document.getElementById('panel-close');
                const assetPanel = document.getElementById('asset-panel');
                const panelBackdrop = document.getElementById('panel-backdrop');
                const assetContent = document.getElementById('cross-asset-content');

                // Open panel function
                function openPanel() {
                    assetPanel.classList.add('active');
                    panelBackdrop.classList.add('active');
                    document.body.style.overflow = 'hidden';

                    // Change icon to close
                    const icon = panelToggle.querySelector('i');
                    icon.className = 'ti ti-x';
                    panelToggle.title = 'Close Asset Panel';
                }

                // Close panel function
                function closePanel() {
                    assetPanel.classList.remove('active');
                    panelBackdrop.classList.remove('active');
                    document.body.style.overflow = 'auto';

                    // Change icon back to list
                    const icon = panelToggle.querySelector('i');
                    icon.className = 'ti ti-device-desktop';
                    panelToggle.title = 'Asset Panel';

                    // Trigger resize event to make sure chart redraws correctly
                    if (crossAssetChart) {
                        setTimeout(function() {
                            window.dispatchEvent(new Event('resize'));
                            crossAssetChart.render();
                        }, 300);
                    }
                }

                // Toggle panel
                panelToggle.addEventListener('click', function() {
                    if (assetPanel.classList.contains('active')) {
                        closePanel();
                    } else {
                        openPanel();
                    }
                });

                // Close button
                panelClose.addEventListener('click', closePanel);

                // Close when clicking backdrop
                panelBackdrop.addEventListener('click', closePanel);

                // Close on ESC key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && assetPanel.classList.contains('active')) {
                        closePanel();
                    }
                });

                // Swipe gesture for mobile
                let startX = 0;
                let currentX = 0;
                let isSwipeStarted = false;

                assetPanel.addEventListener('touchstart', function(e) {
                    startX = e.touches[0].clientX;
                    isSwipeStarted = true;
                }, {
                    passive: true
                });

                assetPanel.addEventListener('touchmove', function(e) {
                    if (!isSwipeStarted) return;

                    currentX = e.touches[0].clientX;
                    const diffX = currentX - startX;

                    // If swiping right and moved more than 100px, close panel
                    if (diffX > 100) {
                        closePanel();
                        isSwipeStarted = false;
                    }
                }, {
                    passive: true
                });

                assetPanel.addEventListener('touchend', function() {
                    isSwipeStarted = false;
                }, {
                    passive: true
                });

                // Handle window resize
                window.addEventListener('resize', function() {
                    if (crossAssetChart) {
                        crossAssetChart.render();
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
