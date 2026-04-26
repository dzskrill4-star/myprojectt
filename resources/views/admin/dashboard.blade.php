@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="row gy-4">
                <div class="col-xxl-3 col-sm-6">
                    <x-widget style="2" link="{{ route('admin.users.all') }}" icon="las la-users" title="Total Users" value="{{ $widget['total_users'] }}" color="primary" icon_style="solid" />
                </div><!-- dashboard-w1 end -->
                <div class="col-xxl-3 col-sm-6">
                    <x-widget style="2" link="{{ route('admin.users.active') }}" icon="las la-user-check" title="Active Users" value="{{ $widget['verified_users'] }}" color="success" icon_style="solid" />
                </div><!-- dashboard-w1 end -->
                <div class="col-xxl-3 col-sm-6">
                    <x-widget style="2" link="{{ route('admin.users.email.unverified') }}" icon="lar la-envelope" title="Email Unverified Users" value="{{ $widget['email_unverified_users'] }}" color="danger" icon_style="solid" />
                </div><!-- dashboard-w1 end -->
                <div class="col-xxl-3 col-sm-6">
                    <x-widget style="2" link="{{ route('admin.users.mobile.unverified') }}" icon="las la-comment-slash" title="Mobile Unverified Users" value="{{ $widget['mobile_unverified_users'] }}" color="warning" icon_style="solid" />
                </div><!-- dashboard-w1 end -->

                <div class="col-xxl-3 col-sm-6">
                    <x-widget value="{{ $widget['total_miner'] }}" title="Total Currencies" style="2" color="info" icon_style="solid" link="{{ route('admin.currency.index') }}" icon="la la-hammer" icon_style="solid" />
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <x-widget value="{{ $widget['total_plan'] }}" title="Total Mining Plan" style="2" color="primary" icon_style="solid" link="{{ route('admin.plan.index') }}" icon="la la-list" icon_style="solid" />
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <x-widget value="{{ $widget['total_order_count'] }}" title="Total Orders" style="2" color="success" icon_style="solid" link="{{ route('admin.order.index') }}" icon="la la-list-alt" icon_style="solid" />
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <x-widget value="{{ showAmount($widget['total_order_amount']) }}" title="Total Order Amount" style="2" color="dark" icon_style="solid" icon="la la-money-bill" icon_style="solid" />
                </div>
            </div><!-- row end-->
        </div>

        <div class="col-12">
            <div class="row gy-4">
                <div class="col-xxl-6">
                    <div class="card box-shadow3 h-100">
                        <div class="card-body">
                            <h5 class="card-title">@lang('Deposits')</h5>
                            <div class="widget-card-wrapper">

                                <div class="widget-card bg--success">
                                    <a href="{{ route('admin.deposit.successful') }}" class="widget-card-link"></a>
                                    <div class="widget-card-left">
                                        <div class="widget-card-icon">
                                            <i class="fas fa-hand-holding-usd"></i>
                                        </div>
                                        <div class="widget-card-content">
                                            <h6 class="widget-card-amount">{{ showAmount($deposit['total_deposit_amount']) }}</h6>
                                            <p class="widget-card-title">@lang('Total Successful Amount')</p>
                                        </div>
                                    </div>

                                    <span class="widget-card-arrow">
                                        <i class="las la-angle-right"></i>
                                    </span>
                                </div>

                                <div class="widget-card bg--warning">
                                    <a href="{{ route('admin.deposit.pending') }}" class="widget-card-link"></a>
                                    <div class="widget-card-left">
                                        <div class="widget-card-icon">
                                            <i class="fas fa-spinner"></i>
                                        </div>
                                        <div class="widget-card-content">
                                            <h6 class="widget-card-amount">{{ showAmount($deposit['total_deposit_pending'], exceptZeros: true) }}</h6>
                                            <p class="widget-card-title">@lang('Total Pending Amount')</p>
                                        </div>
                                    </div>
                                    <span class="widget-card-arrow">
                                        <i class="las la-angle-right"></i>
                                    </span>
                                </div>

                                <div class="widget-card bg--danger">
                                    <a href="{{ route('admin.deposit.rejected') }}" class="widget-card-link"></a>
                                    <div class="widget-card-left">
                                        <div class="widget-card-icon">
                                            <i class="fas fa-ban"></i>
                                        </div>
                                        <div class="widget-card-content">
                                            <h6 class="widget-card-amount">{{ showAmount($deposit['total_deposit_rejected'], exceptZeros: true) }}</h6>
                                            <p class="widget-card-title">@lang('Total Rejected Amount')</< /p>
                                        </div>
                                    </div>
                                    <span class="widget-card-arrow">
                                        <i class="las la-angle-right"></i>
                                    </span>
                                </div>

                                <div class="widget-card bg--primary">
                                    <a href="{{ route('admin.deposit.list') }}" class="widget-card-link"></a>
                                    <div class="widget-card-left">
                                        <div class="widget-card-icon">
                                            <i class="fas fa-percentage"></i>
                                        </div>
                                        <div class="widget-card-content">
                                            <h6 class="widget-card-amount">{{ showAmount($deposit['total_deposit_charge']) }}</h6>
                                            <p class="widget-card-title">@lang('Total Deposit Charge')</p>
                                        </div>
                                    </div>
                                    <span class="widget-card-arrow">
                                        <i class="las la-angle-right"></i>
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-6">
                    <div class="card box-shadow3 h-100">
                        <div class="card-body">
                            <h5 class="card-title">@lang('Withdrawals')</h5>
                            <div class="widget-card-wrapper">
                                <div class="widget-card bg--success">
                                    <a href="{{ route('admin.withdraw.data.all') }}" class="widget-card-link"></a>
                                    <div class="widget-card-left">
                                        <div class="widget-card-icon">
                                            <i class="lar la-credit-card"></i>
                                        </div>
                                        <div class="widget-card-content">
                                            <h6 class="widget-card-amount">{{ showAmount($withdrawals['total_withdraw_amount']) }}</h6>
                                            <p class="widget-card-title">@lang('Total Withdrawn')</p>
                                        </div>
                                    </div>
                                    <span class="widget-card-arrow">
                                        <i class="las la-angle-right"></i>
                                    </span>
                                </div>

                                <div class="widget-card bg--warning">
                                    <a href="{{ route('admin.withdraw.data.pending') }}" class="widget-card-link"></a>
                                    <div class="widget-card-left">
                                        <div class="widget-card-icon">
                                            <i class="fas fa-spinner"></i>
                                        </div>
                                        <div class="widget-card-content">
                                            <h6 class="widget-card-amount">{{ $withdrawals['total_withdraw_pending'] }}</h6>
                                            <p class="widget-card-title">@lang('Pending Withdrawals')</p>
                                        </div>
                                    </div>
                                    <span class="widget-card-arrow">
                                        <i class="las la-angle-right"></i>
                                    </span>
                                </div>

                                <div class="widget-card bg--danger">
                                    <a href="{{ route('admin.withdraw.data.rejected') }}" class="widget-card-link"></a>
                                    <div class="widget-card-left">
                                        <div class="widget-card-icon">
                                            <i class="las la-times-circle"></i>
                                        </div>
                                        <div class="widget-card-content">
                                            <h6 class="widget-card-amount">{{ $withdrawals['total_withdraw_rejected'] }}</h6>
                                            <p class="widget-card-title">@lang('Rejected Withdrawals')</p>
                                        </div>
                                    </div>
                                    <span class="widget-card-arrow">
                                        <i class="las la-angle-right"></i>
                                    </span>
                                </div>

                                <div class="widget-card bg--primary">
                                    <a href="{{ route('admin.withdraw.data.all') }}" class="widget-card-link"></a>
                                    <div class="widget-card-left">
                                        <div class="widget-card-icon">
                                            <i class="las la-percent"></i>
                                        </div>
                                        <div class="widget-card-content">
                                            <h6 class="widget-card-amount">{{ showAmount($withdrawals['total_withdraw_charge']) }}</h6>
                                            <p class="widget-card-title">@lang('Withdrawal Charge')</p>
                                        </div>
                                    </div>
                                    <span class="widget-card-arrow">
                                        <i class="las la-angle-right"></i>
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="row gy-4">
                <div class="col-xxl-3 col-sm-6">
                    <x-widget value="{{ $miningTrack['total_tracks'] }}" title="Total Mining Tracks" style="2" color="info" icon_style="solid" link="{{ route('admin.mining.tracks.all') }}" icon="la la-hammer" icon_style="solid" />
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <x-widget value="{{ $miningTrack['active_tracks'] }}" title="Active Mining Tracks" style="2" color="primary" icon_style="solid" link="{{ route('admin.mining.tracks.active') }}" icon="la la-hammer" icon_style="solid" />
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <x-widget value="{{ showAmount($miningTrack['total_returned']) }}" title="Total Returned" style="2" color="success" icon_style="solid" link="{{ route('admin.report.transaction') }}?remark=return_amount" icon="la la-undo" icon_style="solid" />
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <x-widget value="{{ showAmount($miningTrack['total_maintenance_cost']) }}" title="Total Maintenance Cost" style="2" color="dark" link="{{ route('admin.report.transaction') }}?remark=maintenance_cost" icon_style="solid" icon="la la-redo" icon_style="solid" />
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="row gy-4">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap gap-3">
                                <h5 class="card-title mb-3">@lang('Returned Amount')</h5>
                                <div class="d-flex flex-wrap gap-3">
                                    <select class="form-control w-auto" name="currency" id="returnAmoCurrency">
                                        @foreach ($cryptoCurrencies as $cryptoCurrency)
                                            <option value="{{ $cryptoCurrency }}">{{ strtoupper($cryptoCurrency) }}</option>
                                        @endforeach
                                    </select>
                                    <div id="returnAmoDatePicker" class="border daterangepicker-selectbox rounded">
                                        <i class="la la-calendar"></i>&nbsp;
                                        <span></span> <i class="la la-caret-down"></i>
                                    </div>
                                </div>
                            </div>
                            <div id="returnedAmountChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap gap-3">
                                <h5 class="card-title mb-3">@lang('Transactions')</h5>

                                <div class="d-flex flex-wrap gap-3">
                                    <select class="form-control w-auto" name="currency" id="trxCurrency">
                                        @foreach ($allCurrencies as $currency)
                                            <option value="{{ $currency }}" @selected($currency == gs()->cur_text)>{{ strtoupper($currency) }}</option>
                                        @endforeach
                                    </select>

                                    <div id="trxDatePicker" class="border daterangepicker-selectbox rounded">
                                        <i class="la la-calendar"></i>&nbsp;
                                        <span></span> <i class="la la-caret-down"></i>
                                    </div>
                                </div>
                            </div>
                            <div id="transactionChartArea"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="row gy-4">
                <div class="col-xl-4 col-lg-6">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <h5 class="card-title">@lang('Login By Browser') (@lang('Last 30 days'))</h5>
                            <canvas id="userBrowserChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">@lang('Login By OS') (@lang('Last 30 days'))</h5>
                            <canvas id="userOsChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">@lang('Login By Country') (@lang('Last 30 days'))</h5>
                            <canvas id="userCountryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    @include('admin.partials.cron_modal')
@endsection
@push('breadcrumb-plugins')
    <button class="btn btn-outline--primary btn-sm" data-bs-toggle="modal" data-bs-target="#cronModal">
        <i class="las la-server"></i>@lang('Cron Setup')
    </button>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        "use strict";

        const start = moment().subtract(14, 'days');
        const end = moment();

        const dateRangeOptions = {
            startDate: start,
            endDate: end,
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
            maxDate: moment()
        }

        const changeDatePickerText = (element, startDate, endDate, label) => {
            if (label == 'Custom Range') {
                $(element).html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
            } else {
                $(element).html(label);
            }
        }

        const initialData = [{
            name: 'Returned Amount',
            data: []
        }]
        let returnAmountChart = barChart(document.querySelector("#returnedAmountChart"), $('#returnAmoCurrency').val(), initialData, []);

        let trxChart = lineChart(
            document.querySelector("#transactionChartArea"),
            [{
                    name: "Plus Transactions",
                    data: []
                },
                {
                    name: "Minus Transactions",
                    data: []
                }
            ],
            []
        );

        const setReturnAmountChart = (startDate, endDate) => {

            let currency = $('#returnAmoCurrency').val();
            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD'),
                currency
            }

            const url = `{{ route('admin.chart.return.amount') }}`;

            $.get(url, data,
                function(data, status) {
                    if (status == 'success') {
                        returnAmountChart.updateSeries(data.data);
                        returnAmountChart.updateOptions({
                            xaxis: {
                                categories: data.created_on,
                            },
                            yaxis: {
                                title: {
                                    text: data.currency,
                                    style: {
                                        color: '#7c97bb'
                                    }
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: function(value) {
                                        return data.currency + ' ' + value.toFixed(4);
                                    }
                                }
                            }
                        });
                    }
                }
            );
        }

        const transactionChart = (startDate, endDate) => {
            let currency = $('#trxCurrency').val();

            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD'),
                currency
            }

            const url = `{{ route('admin.chart.transaction') }}`;

            $.get(url, data,
                function(data, status) {
                    if (status == 'success') {
                        trxChart.updateSeries(data.data);
                        trxChart.updateOptions({
                            xaxis: {
                                categories: data.created_on,
                            }
                        });
                    }
                }
            );
        }

        $('#returnAmoDatePicker').daterangepicker(dateRangeOptions, (start, end, label) => changeDatePickerText('#returnAmoDatePicker span', start, end, label));
        $('#trxDatePicker').daterangepicker(dateRangeOptions, (start, end, label) => changeDatePickerText('#trxDatePicker span', start, end, label));

        setReturnAmountChart(start, end);
        transactionChart(start, end);

        changeDatePickerText('#returnAmoDatePicker span', start, end, 'Last 15 Days');

        $('#returnAmoDatePicker').on('apply.daterangepicker', (event, picker) => setReturnAmountChart(picker.startDate, picker.endDate));
        $('#trxDatePicker').on('apply.daterangepicker', (event, picker) => transactionChart(picker.startDate, picker.endDate));

        $("#trxCurrency").on("change", function() {
            let startDate = $("#trxDatePicker").data('daterangepicker').startDate._d;
            let endDate = $("#trxDatePicker").data('daterangepicker').endDate._d;

            transactionChart(moment(startDate), moment(endDate));
        });


        changeDatePickerText('#trxDatePicker span', start, end, 'Last 15 Days');


        $("#returnAmoCurrency").on("change", function() {
            let startDate = $("#returnAmoDatePicker").data('daterangepicker').startDate._d;
            let endDate = $("#returnAmoDatePicker").data('daterangepicker').endDate._d;

            setReturnAmountChart(moment(startDate), moment(endDate));
        });

        piChart(
            document.getElementById('userBrowserChart'),
            JSON.parse(`@php echo json_encode($chart['user_browser_counter']->keys()); @endphp`),
            JSON.parse(`@php echo json_encode($chart['user_browser_counter']->flatten()); @endphp`)
        );

        piChart(
            document.getElementById('userOsChart'),
            JSON.parse(`@php echo json_encode($chart['user_os_counter']->keys()); @endphp`),
            JSON.parse(`@php echo json_encode($chart['user_os_counter']->flatten()); @endphp`)
        );

        piChart(
            document.getElementById('userCountryChart'),
            JSON.parse(`@php echo json_encode($chart['user_country_counter']->keys()); @endphp`),
            JSON.parse(`@php echo json_encode($chart['user_country_counter']->flatten()); @endphp`)
        );
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
            cursor: pointer !important;
        }

        .daterangepicker-selectbox i {
            line-height: 35px;
        }

        .input.form-control,
        select.form-control {
            height: 35px;
        }
    </style>
@endpush
