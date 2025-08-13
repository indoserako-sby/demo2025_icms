<div wire:id="asset-analysis-chart">
    <div id="asset-chart-container" class="position-relative" style="min-height: 700px;">
        <div id="asset-chart" style="width: 100%; height: 700px;"></div>
    </div>

    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // Initialize ECharts instance
                let chartInstance = null;

                // Handler for updateChart event
                document.addEventListener('updateChart', function(event) {

                    // Get the chart data and handle case where it might be wrapped in an array
                    let chartData = event.detail;

                    // If chartData is an array with one item, unwrap it
                    if (Array.isArray(chartData) && chartData.length === 1) {
                        chartData = chartData[0];
                    }


                    // Validate chart data
                    if (!chartData) {
                        console.warn('⚠️ No chart data received');
                        displayNoDataMessage('No chart data received');
                        return;
                    }

                    // Check if series exists and is an array
                    if (!Array.isArray(chartData.series)) {
                        console.warn('⚠️ Invalid chart data format: series is not an array', chartData);
                        displayNoDataMessage('Invalid chart data format');
                        return;
                    }

                    // Check if we have any series data
                    if (chartData.series.length === 0) {
                        console.warn('⚠️ No chart data to display: empty series');
                        displayNoDataMessage('No data available for the selected parameters and time period');
                        return;
                    }

                    // Additional validation: check if series items have data property and it's not empty
                    const hasValidData = chartData.series.some(series =>
                        Array.isArray(series.data) && series.data.length > 0
                    );

                    if (!hasValidData) {
                        console.warn('⚠️ No chart data to display: no data points');
                        displayNoDataMessage('No data available for the selected time period');
                        return;
                    }

                    // Get date range from chart data
                    const dateRange = chartData.dateRange || {};
                    const minDate = dateRange.start ? new Date(dateRange.start).getTime() : null;
                    const maxDate = dateRange.end ? new Date(dateRange.end).getTime() : null;

                    // Render chart with new data
                    renderChart(chartData, minDate, maxDate);
                });

                function renderChart(data, minDate, maxDate) {


                    const chartElement = document.getElementById('asset-chart');
                    if (!chartElement) {
                        console.error('❌ Chart element not found');
                        return;
                    }

                    // Process series data for ECharts
                    const series = data.series.map((s, index) => ({
                        name: s.name,
                        type: 'line',
                        smooth: true,
                        symbolSize: 6,
                        data: s.data.map(point => {
                            const timestamp = new Date(point[0]).getTime();
                            return [timestamp, point[1]];
                        }),
                        yAxisIndex: index // Each series gets its own y-axis
                    }));

                    // Process y-axes configuration
                    const yAxis = data.yAxis.map((axis, index) => ({
                        name: axis.name + (axis.unit ? ` (${axis.unit})` : ''),
                        type: 'value',
                        tickAmount: 5,


                        position: index % 2 === 0 ? 'left' : 'right',
                        offset: Math.floor(index / 2) * 80, // Offset to prevent overlap
                        min: axis.min,
                        max: axis.max,
                        axisLabel: {
                            formatter: '{value}'
                        },
                        splitLine: {
                            show: true,
                            lineStyle: {
                                type: 'dashed'
                            }
                        }
                    }));

                    // Configure ECharts options
                    const options = {
                        title: {
                            text: data.title || 'Asset Parameter Analysis',
                            left: 'center'
                        },
                        tooltip: {

                            trigger: 'axis',
                            axisPointer: {
                                type: 'cross'
                            },
                            formatter: function(params) {
                                const date = new Date(params[0].value[0]);
                                let tooltipText = date.toLocaleString() + '<br/>';
                                params.forEach((param, index) => {
                                    const unit = data.yAxis[index].unit || '';
                                    tooltipText +=
                                        `${param.marker} ${param.seriesName}: ${param.value[1].toFixed(2)}${unit}<br/>`;
                                });
                                return tooltipText;
                            }
                        },
                        legend: {
                            data: series.map(s => s.name),
                            top: 25
                        },
                        grid: {
                            top: 90,
                            bottom: 50,
                            right: 80 + (Math.ceil(series.length / 2) - 1) * 80,
                            containLabel: true
                        },
                        xAxis: {
                            type: 'time',
                            boundaryGap: false,
                            min: minDate,
                            max: maxDate,
                            axisLabel: {
                                formatter: (value) => {
                                    const date = new Date(value);
                                    return date.toLocaleDateString() + '\n' + date.toLocaleTimeString();
                                }
                            }
                        },
                        yAxis: yAxis,
                        series: series,
                        dataZoom: [{
                            type: 'inside',
                            start: 0,
                            end: 100
                        }, {
                            type: 'slider',
                            start: 0,
                            end: 100
                        }],
                        toolbox: {
                            feature: {
                                dataZoom: {
                                    yAxisIndex: 'none'
                                },
                                restore: {},
                                saveAsImage: {}
                            }
                        }
                    };

                    // Initialize or update chart
                    if (!chartInstance) {
                        chartInstance = echarts.init(chartElement);
                    }

                    // Set options and render
                    chartInstance.setOption(options, true);

                    // Handle window resize
                    window.addEventListener('resize', function() {
                        if (chartInstance) {
                            chartInstance.resize();
                        }
                    });
                }

                function displayNoDataMessage(message) {
                    const chartElement = document.getElementById('asset-chart');
                    if (chartElement) {
                        if (chartInstance) {
                            chartInstance.dispose();
                            chartInstance = null;
                        }
                        chartElement.innerHTML = `
                            <div class="d-flex justify-content-center align-items-center h-100">
                                <div class="text-center text-muted">
                                    <i class="ti ti-chart-bar-off mb-3" style="font-size: 3rem;"></i>
                                    <p>${message || 'No data available'}</p>
                                </div>
                            </div>
                        `;
                    }
                }

                // Initialize with empty chart
                displayNoDataMessage('Please select parameters to view the chart');
            });
        </script>
    @endpush
</div>
