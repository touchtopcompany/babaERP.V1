@extends('layouts.app')

@section('title', __('accounting::lang.accounting'))

@section('content')
    @include('accounting::layouts.nav')
    
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="form-filters clearfix mb-5">
                    <div class="form-inline pull-right">
                        <!-- Location Filter -->
                        <div class="form-group" style="margin-right: 10px;">
                            {!! Form::select('dashboard_location_filter', $business_locations, request()->input('location_id', null), [
                                'id' => 'dashboard_location_filter',
                                'class' => 'form-control select2',
                                'style' => 'width: 200px;',
                                'placeholder' => __('messages.please_select')
                            ]) !!}
                        </div>

                        <!-- Date Filter Button -->
                        <div class="form-group">
                            <div class="input-group">
                                <button type="button" class="btn btn-primary" id="dashboard_date_filter">
                            <span>
                                <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                            </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-primary', 
                'title' => __('accounting::lang.chart_of_account_overview')])
                    <div class="col-md-4">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('accounting::lang.account_type')</th>
                                    <th>@lang('accounting::lang.current_balance')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($account_types as $k => $v)
                                    @php
                                        $bal = 0;
                                        foreach($coa_overview as $overview) {
                                            if($overview->account_primary_type==$k && !empty($overview->balance)) {
                                                $bal = (float)$overview->balance;
                                            }
                                        }
                                    @endphp

                                    <tr>
                                        <td>
                                            {{$v['label']}}

                                            {{-- Suffix CR/DR as per value --}}
                                            @if($bal < 0)
                                                {{ (in_array($v['label'], ['Asset', 'Expenses']) ? ' (CR)' : ' (DR)') }}
                                            @endif
                                        </td>
                                        <td>
                                            @format_currency(abs($bal))
                                        </td>
                                    </tr>
                                    
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-8">
                        {!! $coa_overview_chart->container() !!}
                    </div>
                @endcomponent
            </div>
        </div>

        <div class="row">
            @foreach($all_charts as $key => $chart)
            <div class="col-md-6">
                @component('components.widget', ['class' => 'box-primary', 
                'title' => __('accounting::lang.' . $key)])
                {!! $chart->container() !!}
                @endcomponent
            </div>
            @endforeach
        </div>
    </section>
@stop

@section('javascript')
{!! $coa_overview_chart->script() !!}
@foreach($all_charts as $key => $chart)
{!! $chart->script() !!}

<script type="text/javascript">
    $(document).ready(function () {
        // Set initial start and end dates
        dateRangeSettings.startDate = moment('{{$start_date}}', 'YYYY-MM-DD');
        dateRangeSettings.endDate = moment('{{$end_date}}', 'YYYY-MM-DD');

        /**
         * Update the span text inside the date range picker
         */
        function updateDateRangeDisplay(start, end) {
            $('#dashboard_date_filter span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
        }

        /**
         * Get the selected start and end date in 'YYYY-MM-DD' format
         */
        function getSelectedDates() {
            const picker = $('#dashboard_date_filter').data('daterangepicker');
            return {
                start: picker.startDate.format('YYYY-MM-DD'),
                end: picker.endDate.format('YYYY-MM-DD')
            };
        }

        /**
         * Get the currently selected location ID
         */
        function getSelectedLocationId() {
            return $('#dashboard_location_filter').val();
        }

        /**
         * Build the URL for redirection
         */
        function buildDashboardUrl(start, end, location_id) {
            return (
                "{{ action([\Modules\Accounting\Http\Controllers\AccountingController::class, 'dashboard']) }}" +
                "?start_date=" + encodeURIComponent(start) +
                "&end_date=" + encodeURIComponent(end) +
                "&location_id=" + encodeURIComponent(location_id)
            );
        }

        /**
         * Redirect the user to the dashboard with current filter values
         */
        function redirectToDashboard() {
            const { start, end } = getSelectedDates();
            const location_id = getSelectedLocationId();
            const url = buildDashboardUrl(start, end, location_id);

            window.location.href = url;
        }

        // Initialize date range picker
        $('#dashboard_date_filter').daterangepicker(dateRangeSettings, function (start, end) {
            updateDateRangeDisplay(start, end);
            redirectToDashboard();
        });

        // Handle location filter change
        $('#dashboard_location_filter').change(function () {
            redirectToDashboard();
        });
    });

</script>
@endforeach


@stop