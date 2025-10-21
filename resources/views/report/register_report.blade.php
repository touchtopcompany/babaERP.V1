@extends('layouts.app')
@section('title', __('report.register_report'))
@section('css')
@if(session('status.warning'))
    <style>
        #toast-container {
            top: 80px !important;
        }
    </style>
@endif
@endsection
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.register_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
              {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getRegisterReport']), 'method' => 'get', 'id' => 'register_report_filter_form' ]) !!}
               <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('sell_list_filter_location_id',  __('purchase.business_location') . ':') !!}

                            {!! Form::select('sell_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', count($business_locations) == 1 ? "" :'placeholder' => __('lang_v1.all')  ]); !!}
                        </div>
                    </div>  
              <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('register_user_id',  __('report.user') . ':') !!}
                        {!! Form::select('register_user_id', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('report.all_users')]); !!}
                    </div>
                </div>
            
              

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('register_status',  __('sale.status') . ':') !!}
                        {!! Form::select('register_status', ['open' => __('cash_register.open'), 'close' => __('cash_register.close')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('report.all')]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('register_report_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('register_report_date_range', null , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'register_report_date_range', 'readonly']); !!}
                    </div>
                </div>
                {!! Form::close() !!}
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <table class="table table-bordered table-striped" id="register_report_table">
                    <thead>
                        <tr>
                            <th>@lang('report.open_time')</th>
                            <th>@lang('report.close_time')</th>
                            <th>@lang('sale.location')</th>
                            <th>@lang('report.user')</th>
                            <th>@lang('cash_register.total_card_slips')</th>
                            <th>@lang('cash_register.total_cheques')</th>
                            <th>@lang('cash_register.total_cash')</th>
                            <th>@lang('lang_v1.total_bank_transfer')</th>
                            <th>@lang('lang_v1.total_advance_payment')</th>
                            @foreach($custom_labels as $key => $custom_label)
                                <th>{!! $custom_label !!}</th>
                            @endforeach
                            <th>@lang('cash_register.other_payments')</th>
                            <th>@lang('sale.total')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                            <td class="footer_total_card_payment"></td>
                            <td class="footer_total_cheque_payment"></td>
                            <td class="footer_total_cash_payment"></td>
                            <td class="footer_total_bank_transfer_payment"></td>
                            <td class="footer_total_advance_payment"></td>'
                            @foreach($custom_labels as $key => $custom_label)
                                <td class="footer_total_{{ $key }}"></td>
                            @endforeach
                            <td class="footer_total_other_payments"></td>
                            <td class="footer_total"></td>
                            <td class="footer_cash_to_bank"></td>
                        </tr>
                    </tfoot>
                </table>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

@endsection

@section('javascript')
<script src="{{ asset('js/report.js') }}"></script>
    <script>

        //Moved from script js to blade
        $(document).ready(function (){
            register_report_table = $('#register_report_table').DataTable({
                ajax: '/reports/register-report',
                columns: [
                    {data: 'created_at', name: 'created_at'},
                    {data: 'closed_at', name: 'closed_at'},
                    {data: 'location_name', name: 'bl.name'},
                    {data: 'user_name', name: 'user_name'},
                    {data: 'total_card_payment', name: 'total_card_payment', searchable: false},
                    {data: 'total_cheque_payment', name: 'total_cheque_payment', searchable: false},
                    {data: 'total_cash_payment', name: 'total_cash_payment', searchable: false},
                    {data: 'total_bank_transfer_payment', name: 'total_bank_transfer_payment', searchable: false},
                    {data: 'total_advance_payment', name: 'total_advance_payment', searchable: false},

                    // Loop through custom labels
                        @foreach($custom_labels as $key => $custom_label)
                    {
                        data: "total_{{ $key }}", name: "total_{{ $key }}", searchable: false
                    },
                        @endforeach
                    {
                        data: 'total_other_payment', name: 'total_other_payment', searchable: false
                    },
                    {data: 'total', name: 'total', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "footerCallback": function (row, data, start, end, display) {
                    var total_card_payment = 0;
                    var total_cheque_payment = 0;
                    var total_cash_payment = 0;
                    var total_bank_transfer_payment = 0;
                    var total_other_payment = 0;
                    var total_advance_payment = 0;

                    // Loop through custom labels
                    @foreach($custom_labels as $key => $custom_label)
                    var total_{{ $key }} = 0;
                    @endforeach
                    var total = 0;

                    var total_cash_refund = 0;
                    var total_change = 0
                    let total_cash_return = 0;
                    var debit = 0;


                    for (var r in data) {
                        total_card_payment += $(data[r].total_card_payment).data('orig-value') ?
                            parseFloat($(data[r].total_card_payment).data('orig-value')) : 0;

                        total_cheque_payment += $(data[r].total_cheque_payment).data('orig-value') ?
                            parseFloat($(data[r].total_cheque_payment).data('orig-value')) : 0;

                        total_cash_payment += $(data[r].total_cash_payment).data('orig-value') ?
                            parseFloat($(data[r].total_cash_payment).data('orig-value')) : 0;

                        total_bank_transfer_payment += $(data[r].total_bank_transfer_payment).data('orig-value') ?
                            parseFloat($(data[r].total_bank_transfer_payment).data('orig-value')) : 0;

                        total_other_payment += $(data[r].total_other_payment).data('orig-value') ?
                            parseFloat($(data[r].total_other_payment).data('orig-value')) : 0;

                        total_advance_payment += $(data[r].total_advance_payment).data('orig-value') ?
                            parseFloat($(data[r].total_advance_payment).data('orig-value')) : 0;

                        // Loop through custom labels
                        @foreach($custom_labels as $key => $custom_label)
                            total_{{ $key }} += $(data[r].total_{{ $key }}).data('orig-value')
                            ? parseFloat($(data[r].total_{{$key}}).data('orig-value')) : 0;
                        @endforeach

                            total += $(data[r].total).data('orig-value')
                            ? parseFloat($(data[r].total).data('orig-value')) : 0;

                        //Debit
                        debit = $(data[0].debit).data('orig-value') ?
                            parseFloat($(data[0].debit).data('orig-value')) : 0;

                        total_change = $(data[0].cash_refund_change).data('orig-value') ?
                            parseFloat($(data[0].cash_refund_change).data('orig-value')) : 0;
                        total_cash_return = $(data[0].total_returned).data('orig-value') ?
                            parseFloat($(data[0].total_returned).data('orig-value')) : 0;
                        total_cash_refund += $(data[r].total_cash_refund).data('orig-value') ?
                            parseFloat($(data[r].total_cash_refund).data('orig-value')) : 0;
                    }

                    $('.footer_total_card_payment').html(__currency_trans_from_en(total_card_payment));
                    $('.footer_total_cheque_payment').html(__currency_trans_from_en(total_cheque_payment));
                    $('.footer_total_cash_payment').html(__currency_trans_from_en(total_cash_payment));
                    $('.footer_total_bank_transfer_payment').html(__currency_trans_from_en(total_bank_transfer_payment));
                    $('.footer_total_other_payments').html(__currency_trans_from_en(total_other_payment));
                    $('.footer_total_advance_payment').html(__currency_trans_from_en(total_advance_payment));

                    // Loop through custom labels
                    @foreach($custom_labels as $key => $custom_label)
                    $('.footer_total_{{ $key }}').html(__currency_trans_from_en(total_{{ $key }}))
                    @endforeach
                    $('.footer_total').html(__currency_trans_from_en(total));
                    
                    // console.log('change' + total_change)
                    //                     console.log('return ' + total_cash_return)
                    // console.log('refund' + total_cash_refund)

                    //Working with cash to bank (Cash amount other than payment method eg: card, money transfer)
                    let cash_to_bank = total_cash_payment - (debit + total_cash_return + total_cash_refund + total_change);
                    if (cash_to_bank < 0) {
                        cash_to_bank = 0;
                    }
                    $('.footer_cash_to_bank').css({
                        'font-size': '1.5rem',
                        'font-weight': 500
                    }).html('Cash To Bank <br>' + __currency_trans_from_en(Math.ceil(cash_to_bank)));

                },
                processing: true,
                scrollCollapse: true,
                scrollX: true,
                scrollY: "75vh",
                serverSide: true,
            });

            $('.view_register').on('shown.bs.modal', function() {
                __currency_convert_recursively($(this));
            });

            $(document).on('submit', '#register_report_filter_form', function(e) {
                e.preventDefault();
                updateRegisterReport();
            });

            $('#register_user_id, #register_status, #sell_list_filter_location_id').change(function() {
                updateRegisterReport();
            });
        });

        function updateRegisterReport() {
            var start = $('#register_report_date_range')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            var end = $('#register_report_date_range')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');

            var location = $('#sell_list_filter_location_id').val();


            var data = {
                user_id: $('#register_user_id').val(),
                status: $('#register_status').val(),
                start_date: start,
                end_date: end,
                business_location : location
            };
            var out = [];

            for (var key in data) {
                out.push(key + '=' + encodeURIComponent(data[key]));
            }
            url_data = out.join('&');
            register_report_table.ajax.url('/reports/register-report?' + url_data).load();
        }


        $('#register_report_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#register_report_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                register_report_table.ajax.reload();
            }
        );

        if ($('#register_report_date_range').length == 1) {
            $('#register_report_date_range').daterangepicker({
                ranges: ranges,
                autoUpdateInput: false,
                locale: {
                    format: moment_date_format,
                    cancelLabel: LANG.clear,
                    applyLabel: LANG.apply,
                    customRangeLabel: LANG.custom_range,
                },
            });
            $('#register_report_date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(
                    picker.startDate.format(moment_date_format) +
                    ' ~ ' +
                    picker.endDate.format(moment_date_format)
                );
                updateRegisterReport();
            });

            $('#register_report_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                updateRegisterReport();
            });
        }

    </script>
@endsection