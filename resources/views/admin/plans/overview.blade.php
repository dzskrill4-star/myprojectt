@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-sm-6 col-xl-4">
            <x-widget style="2" link="{{ route('admin.mining.tracks.all') }}?plan_id={{ $plan->id }}" icon="la la-hammer" title="Total Mining Tracks" value="{{ $plan->total_mining_tracks }}" color="primary" icon_style="solid" overlay_icon="0" />
        </div>

        <div class="col-sm-6 col-xl-4">
            <x-widget style="2" link="{{ route('admin.mining.tracks.active') }}?plan_id={{ $plan->id }}" icon="la la-hammer" title="Active Mining Tracks" value="{{ $plan->active_mining_tracks }}" color="success" icon_style="solid" overlay_icon="0" />
        </div>

        <div class="col-sm-6 col-xl-4">
            <x-widget style="2" link="{{ route('admin.mining.tracks.completed') }}?plan_id={{ $plan->id }}" icon="la la-hammer" title="Completed Mining Tracks" value="{{ $plan->completed_mining_tracks }}" color="dark" icon_style="solid" overlay_icon="0" />
        </div>

        <div class="col-sm-6 col-xl-4">
            <x-widget style="2" link="{{ route('admin.order.index') }}?plan_id={{ $plan->id }}" icon="la la-cart-arrow-down" title="Total Ordered Amount" value="{{ showAmount($plan->total_ordered_amount, exceptZeros: true) }}" color="1" icon_style="solid" overlay_icon="0" />
        </div>

        <div class="col-sm-6 col-xl-4">
            <x-widget style="2" link="{{ route('admin.report.transaction') }}?plan_id={{ $plan->id }}&remark=return_amount" icon="la la-rotate-left" title="Total Return Amount" value="{{ showAmount($totalReturnAmount, 8, exceptZeros: true, currencyFormat: false) }} {{ strtoupper($currency) }}" color="warning" icon_style="solid" overlay_icon="0" />
        </div>

        <div class="col-sm-6 col-xl-4">
            <x-widget style="2" link="{{ route('admin.report.transaction') }}?plan_id={{ $plan->id }}&remark=maintenance_cost" icon="las la-redo" title="Total Maintenance Cost" value="{{ showAmount($totalMaintenanceAmount, 8, exceptZeros: true, currencyFormat: false) }} {{ strtoupper($currency) }}" color="info" icon_style="solid" overlay_icon="0" />
        </div>


        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5>@lang('Order Amount by Date')</h5>

                        <div id="ordersByDateDatePicker" class="border daterangepicker-selectbox rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area chart-area--fixed">
                        <div id="ordersByDateChart"></div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->

        <div class="col-xxl-6">
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
        </div><!-- dashboard-w1 end -->

    </div><!-- row end-->
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.plan.index') }}" />
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

@push('script')
    <script>
        (function($) {
            "use strict";
            const planId = `{{ $plan->id }}`;
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
                    plan_id: planId
                }
                $.ajax({
                    type: "GET",
                    url: "{{ route('admin.plan.order.analytics') }}",
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
            setOrderChart(startDate, endDate, planId);

            $('#ordersByDateDatePicker').on('apply.daterangepicker', (event, picker) => setOrderChart(picker.startDate, picker.endDate));

            const returnChart = barChart(document.querySelector("#returnsByDateChart"), `{{ $currency }}`, [], []);

            const setReturnChart = (startDate, endDate, planId = null) => {
                const data = {
                    start_date: startDate.format('YYYY-MM-DD'),
                    end_date: endDate.format('YYYY-MM-DD'),
                    plan_id: planId
                }

                $.ajax({
                    type: "GET",
                    url: "{{ route('admin.plan.return.analytics') }}",
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

            setReturnChart(startDate, endDate, planId);
        })(jQuery);
    </script>
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
