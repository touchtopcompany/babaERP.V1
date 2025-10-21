@extends('layouts.app')
@section('title', __('report.stock_transfer'))

@section('css')
    <style>
        .bg-danger{
            background-color: #c11a1a !important;
        }
        .fa {
            display: inline !important;
        }
    </style>

@endsection
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('report.stock_transfer')
        </h1>
    </section>



    <!-- Main content -->
    <section class="content no-print">
        <div class="row">
            <div class="col-md-12">
                @component('components.filters', ['title' => __('report.filters')])
                    {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getStockReport']), 'method' => 'get', 'id' => 'register_report_filter_form' ]) !!}
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('sell_list_filter_location_id',  __('lang_v1.location_from')) !!}
                            {!! Form::select('purchase_list_filter_location_id', $from_business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id'=>'from_location_id' ]); !!}
                            {{--                        {!! Form::select('sell_list_filter_location_id', $from_business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%','id'=>'from_location_id' ,'placeholder' => __('lang_v1.all')]); !!}--}}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('sell_list_filter_location_id',  __('lang_v1.location_to')) !!}
                            {!! Form::select('sell_list_filter_location_id', $to_business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%','id'=>'to_location_id' ,'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
                            {!! Form::text('sell_list_filter_date_range', null , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'sell_list_filter_date_range', 'readonly']); !!}
                        </div>
                    </div>

                    {!! Form::close() !!}
                @endcomponent
            </div>
        </div>
        @component('components.widget', ['class' => 'box-primary'])
            <div class="table-responsive">
                <table class="table table-bordered table-striped ajax_view" id="stock_transfer_report_table">
                    <thead>
                    <tr>
                        <th>@lang('messages.date')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('business.product')</th>
                        <th>@lang('product.sku')</th>
                        <th>@lang('lang_v1.location_from')</th>
                        <th>@lang('lang_v1.location_to')</th>
                        <th>@lang('lang_v1.quantity')</th>
                        <th>@lang('Unit Price by Selling Price')</th>
                        <th>@lang('stock_adjustment.total_amount')</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                    <tr class="bg-gray font-14 footer-total text-center">
                        <td colspan="3"><strong>@lang('sale.total'):</strong></td>
                        <td colspan="3" class="footer_total_shipping"></td>
                        <td colspan="3" class="footer_total_amount"></td>

                    </tr>
                    </tfoot>
                </table>
            </div>
        @endcomponent
    </section>


    <section id="receipt_section" class="print_section"></section>

    <!-- /.content -->
@stop
@section('javascript')


    <script>
        $(document).ready(function () {
            stock_transfer_report_table = $('#stock_transfer_report_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[0, 'desc']],
                "ajax": {
                    "url": "/reports/stock-transfer-report",
                    "data": function (d) {

                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                        d.from_location_id = $('#from_location_id').val();
                        d.to_location_id = $('#to_location_id').val();


                        d = __datatable_ajax_callback(d);
                    },
                },

                // columnDefs: [
                //     {
                //         targets: 9,
                //         orderable: false,
                //         searchable: false,
                //     },
                // ],
                columns: [
                    {data: 'transaction_date', name: 'transaction_date'},
                    {data: 'ref_no', name: 'ref_no'},
                    {data: 'product', name: 'product'},
                    {data: 'sub_sku', name: 'sub_sku'},
                    {data: 'location_from', name: 'l1.name'},
                    {data: 'location_to', name: 'l2.name'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'unit_price', name: 'unit_price'},
                    {data: 'final_total', name: 'final_total'},
                    // {data: 'net_amount', name: 'net_amount'},
                ],
                fnDrawCallback: function (oSettings) {
                    __currency_convert_recursively($('#stock_transfer_report_table'));
                },
            });

            $('#stock_transfer_report_table').removeClass('no-footer');

            //Date range as a button
            $('#sell_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    stock_transfer_report_table.ajax.reload();
                }
            );
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function (ev, picker) {
                $('#sell_list_filter_date_range').val('');
                stock_transfer_report_table.ajax.reload();
            });

            $(document).on('change', '#from_location_id,#to_location_id, #warehouse_status, #transfer_route, #status', function () {
                stock_transfer_report_table.ajax.reload();
            });
            $(document).on('ifChanged', '#exclude_warehouse,#modified_transfer', function () {
                stock_transfer_report_table.ajax.reload();
            });
        });

    </script>
@endsection