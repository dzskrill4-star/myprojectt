@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-sm-6 col-xl-4">
            <x-widget style="2" link="{{ route('admin.mining.tracks.all') }}?miner_id={{ $miner->id }}" icon="la la-hammer" title="Total Mining Tracks" value="{{ $miner->total_mining_tracks }}" color="primary" icon_style="solid" overlay_icon="0" />
        </div>

        <div class="col-sm-6 col-xl-4">
            <x-widget style="2" link="{{ route('admin.mining.tracks.active') }}?miner_id={{ $miner->id }}" icon="la la-hammer" title="Active Mining Tracks" value="{{ $miner->active_mining_tracks }}" color="success" icon_style="solid" overlay_icon="0" />
        </div>

        <div class="col-sm-6 col-xl-4">
            <x-widget style="2" link="{{ route('admin.mining.tracks.completed') }}?miner_id={{ $miner->id }}" icon="la la-hammer" title="Completed Mining Tracks" value="{{ $miner->completed_mining_tracks }}" color="dark" icon_style="solid" overlay_icon="0" />
        </div>

        <div class="col-sm-6 col-xl-4">
            <x-widget style="2" link="{{ route('admin.report.transaction') }}?currency={{ $miner->currency_code }}&remark=maintenance_cost" icon="las la-redo" title="Total Maintenance Cost" value="{{ showAmount($totalMaintenanceAmount, 8, exceptZeros: true, currencyFormat: false) }} {{ strtoupper($currency) }}" color="info" icon_style="solid" overlay_icon="0" />
        </div>

        <div class="col-sm-6 col-xl-4">
            <x-widget style="2" link="{{ route('admin.order.index') }}?miner_id={{ $miner->id }}" icon="la la-cart-arrow-down" title="Total Ordered Amount" value="{{ showAmount($miner->total_ordered_amount, exceptZeros: true) }}" color="1" icon_style="solid" overlay_icon="0" />
        </div>

        <div class="col-sm-6 col-xl-4">
            <x-widget style="2" link="{{ route('admin.report.transaction') }}?currency={{ $miner->currency_code }}&remark=return_amount" icon="la la-rotate-left" title="Total Return Amount" value="{{ showAmount($totalReturnAmount, 8, exceptZeros: true, currencyFormat: false) }} {{ strtoupper($currency) }}" color="warning" icon_style="solid" overlay_icon="0" />
        </div>

        <div class="col-xl-8">
            <div class="row gy-4">
                <div class="col-xxl-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <h5>@lang('Order Amount by Date')</h5>

                                <div class="d-flex flex-wrap gap-2 ms-auto">
                                    <div>
                                        <select id="ordersByDatePlanPicker" class="form-select">
                                            <option value="" selected>@lang('All')</option>
                                            @foreach ($miner->plans as $plan)
                                                <option value="{{ $plan->id }}">{{ __($plan->title) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="ordersByDateDatePicker" class="border daterangepicker-selectbox rounded">
                                        <i class="la la-calendar"></i>&nbsp;
                                        <span></span> <i class="la la-caret-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-area chart-area--fixed">
                                <div id="ordersByDateChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <h5>@lang('Return Amount by Date')</h5>

                                <div id="returnsByDateDatePicker" class="border daterangepicker-selectbox rounded">
                                    <i class="la la-calendar"></i>&nbsp;
                                    <span></span> <i class="la la-caret-down"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-area chart-area--fixed">
                                <div id="returnsByDateChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Orders By Plan')</h5>
                </div>
                <div class="card-body">
                    <div id="ordersByPlanChart" class="d-flex justify-content-center h-100"></div>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.currency.index') }}" />
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush


@push('style')
    <style>
        .apexcharts-menu {
            min-width: 120px !important;
        }

        .daterangepicker-selectbox {
            height: 35px;
            padding: 0 12px;
            align-items: center !important;
            cursor: pointer !important;
            display: block;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            width: 100%;
            width: max-content;
            max-width: 360px;
        }

        .daterangepicker-selectbox i {
            line-height: 35px;
        }

        .form-select {
            height: 35px;
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        const minerId = `{{ $miner->id }}`;
        const startDate = moment().subtract(6, 'days');
        const endDate = moment();

        const dateRangeOptions = {
            startDate: startDate,
            endDate: endDate,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
            },
            maxDate: moment(),
            applyButtonClasses: "btn--primary"
        }

        const changeDatePickerText = (element, startDate, endDate, label) => {
            if (label == 'Custom Range') {
                $(element).html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
            } else {
                $(element).html(label);
            }
        }

        const orderChart = barChart(document.querySelector("#ordersByDateChart"), `{{ gs('cur_text') }}`, [], []);

        const setOrderChart = (startDate, endDate, planId = null) => {
            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD'),
                miner_id: minerId,
                plan_id: planId
            }
            $.ajax({
                type: "GET",
                url: "{{ route('admin.currency.order.analytics') }}",
                data: data,
                success: function(response) {
                    orderChart.updateSeries(response.data);
                    orderChart.updateOptions({
                        xaxis: {
                            categories: response.created_on
                        }
                    });
                }
            });
        }

        let orderDatePicker = $('#ordersByDateDatePicker').daterangepicker(dateRangeOptions, (startDate, endDate, label) => changeDatePickerText('#ordersByDateDatePicker span', startDate, endDate, label));

        changeDatePickerText('#ordersByDateDatePicker span', startDate, endDate, 'Last 7 Days');
        setOrderChart(startDate, endDate);

        $('#ordersByDateDatePicker').on('apply.daterangepicker', (event, picker) => setOrderChart(picker.startDate, picker.endDate));

        const returnChart = barChart(document.querySelector("#returnsByDateChart"), `{{ $currency }}`, [], []);

        const setReturnChart = (startDate, endDate, planId = null) => {
            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD'),
                miner_id: minerId,
                plan_id: planId
            }

            $.ajax({
                type: "GET",
                url: "{{ route('admin.currency.return.analytics') }}",
                data: data,
                success: function(response) {
                    returnChart.updateSeries(response.data);
                    returnChart.updateOptions({
                        xaxis: {
                            categories: response.created_on
                        }
                    });
                }
            });
        }

        $('#returnsByDateDatePicker').daterangepicker(dateRangeOptions, (startDate, endDate, label) => changeDatePickerText('#returnsByDateDatePicker span', startDate, endDate, label));

        changeDatePickerText('#returnsByDateDatePicker span', startDate, endDate, 'Last 7 Days');

        $('#returnsByDateDatePicker').on('apply.daterangepicker', (event, picker) => setReturnChart(picker.startDate, picker.endDate));

        setReturnChart(startDate, endDate);

        $('#ordersByDatePlanPicker').on('change', function() {
            const dateFormat = 'YYYY-MM-DD';
            const datePicker = $('#ordersByDateDatePicker').data('daterangepicker');
            setOrderChart(datePicker.startDate, datePicker.endDate, this.value);
        });


        var options = {
            series: [{
                data: [],
            }],
            label: [],
            chart: {
                type: 'donut',
                height: 240,
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: false,
            },
            tooltip: {
                y: {
                    formatter: (a) => `{{ gs('cur_sym') }}${a}`,
                    title: {
                        formatter: (seriesName) => seriesName,
                    },
                },
            },
            responsive: [{
                breakpoint: 1399,
                options: {
                    chart: {
                        height: 280
                    }

                }
            }],
            noData: {
                text: 'No data available',
                align: 'center',
                verticalAlign: 'middle',
                offsetX: 0,
                offsetY: 0,
                style: {
                    color: '#999',
                    fontSize: '16px'
                }
            },
        };


        function updateOrdersByPlanFooter(card, labels, series) {
            card.find('.card-footer').remove();

            if (labels.length > 0) {
                let footerContent = '<div class="card-footer p-0 "><ul class="list-group list-group-flush mb-0 py-3">';
                labels.forEach((label, index) => {
                    footerContent += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        ${label}
                        <span class="fw-semi-bold">${series[index]} {{ $currency }}</span>
                    </li>`;
                });
                footerContent += '</ul></div>';
                card.append(footerContent);
            }
        }

        const planChart = new ApexCharts(document.querySelector("#ordersByPlanChart"), options);

        function getPlanWiseOrderData() {
            const data = {
                miner_id: minerId,
            }

            planChart.updateOptions({
                noData: {
                    text: 'Loading...'
                }
            });

            $.ajax({
                url: `{{ route('admin.currency.orders.by.plan') }}`,
                method: 'GET',
                data: data,
                success: function(response) {
                    if (response.series.length) {
                        planChart.updateOptions({
                            labels: response.labels
                        });
                        planChart.updateSeries(response.series);
                        updateOrdersByPlanFooter($('#ordersByPlanChart').parents('.card'), response.labels, response.series);
                    } else {
                        planChart.updateOptions({
                            noData: {
                                text: 'No data found'
                            }
                        });
                    }
                },
                error: function() {
                    planChart.updateOptions({
                        noData: {
                            text: 'Failed to load chart data'
                        }
                    });
                }
            });
        }


        planChart.render();

        getPlanWiseOrderData();

        function sumArray(arr) {
            return arr.reduce((total, num) => total + Number(num), 0);
        }
    </script>
@endpush
