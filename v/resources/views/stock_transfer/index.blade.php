@extends('layouts.app')
@section('title', __('lang_v1.stock_transfers'))

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
    <h1>@lang('lang_v1.stock_transfers')
    </h1>
</section>



<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getStockReport']), 'method' => 'get', 'id' => 'register_report_filter_form' ]) !!}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sell_list_filter_location_id',  __('lang_v1.location_from')) !!}
                                                {!! Form::select('sell_list_filter_location_id', $from_business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%','id'=>'from_location_id']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sell_list_filter_location_id',  __('lang_v1.location_to')) !!}
                        {!! Form::select('sell_list_filter_location_id', $to_business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%','id'=>'to_location_id' ,'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>

                {{--                    @if(!$_is_panone_business)--}}
                {{--                        <div class="col-md-3">--}}
                {{--                            <div class="form-group">--}}
                {{--                                {!! Form::label('register_status',  __('Transfer') . ':') !!}--}}
                {{--    --}}{{--                            @if($_is_panone_business)--}}
                {{--    --}}{{--                                {!! Form::select('warehouse_status', ['inclusive' => __('stock_adjustment.warehouse_inclusive'), 'exclusive' => __('stock_adjustment.warehouse_exclusive')],null, ['class' => 'form-control select2', 'id' => 'warehouse_status' ,'style' => 'width:100%']); !!}--}}
                {{--    --}}{{--                            @else--}}
                {{--                                    {!! Form::select('transfer_route', ['all' => __('stock_adjustment.transfer_all'),'to' => __('stock_adjustment.transfer_to'), 'from' => __('stock_adjustment.transfer_from')],null, ['class' => 'form-control select2', 'id' => 'transfer_route','style' => 'width:100%']); !!}--}}
                {{--                                    --}}{{--                                {!! Form::select('transfer_route', ['all' => __('stock_adjustment.transfer_all'),'to' => __('stock_adjustment.transfer_to'), 'from' => __('stock_adjustment.transfer_from')],null, ['class' => 'form-control select2', 'id' => 'transfer_route','placeholder' => __('stock_adjustment.transfer_all') ,'style' => 'width:100%']); !!}--}}
                {{--    --}}{{--                            @endif--}}
                {{--                            </div>--}}
                {{--                        </div>--}}
                {{--                    @endif--}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('status', __('sale.status')) !!}
                        {!! Form::select('status', $statuses, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'), 'id' => 'status']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('sell_list_filter_date_range', null , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'sell_list_filter_date_range', 'readonly']); !!}
                    </div>
                </div>
                @if($_is_panone_business)
                    <div class="col-md-3">
                        <div class="form-group">
                            <br>
                            <label>
                                {!! Form::checkbox('exclude_warehouse', 1, false, ['class' => 'input-icheck', 'id' => 'exclude_warehouse']); !!}
                                <strong>@lang('stock_adjustment.warehouse_exclusive')</strong>
                            </label>
                        </div>
                    </div>
                @endif
                <div class="col-md-3">
                    <div class="form-group">
                        <br>
                        <label>
                            {!! Form::checkbox('modified_transfer', 1, false, ['class' => 'input-icheck', 'id' => 'modified_transfer']); !!}
                            <strong>@lang('stock_adjustment.modified_transfer')</strong>
                        </label>
                    </div>
                </div>
                {!! Form::close() !!}
            @endcomponent
        </div>
    </div>
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_stock_transfers')])
        @slot('tool')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action([\App\Http\Controllers\StockTransferController::class, 'create'])}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped ajax_view" id="stock_transfer_table">
                <thead>
                    <tr>
                        <th>@lang('messages.date')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('lang_v1.location_from')</th>
                        <th>@lang('lang_v1.location_to')</th>
                        <th>@lang('sale.status')</th>
                        <th>@lang('lang_v1.shipping_charges')</th>
                        <th>@lang('stock_adjustment.total_amount')</th>
                        <th>@lang('purchase.additional_notes')</th>
                        <th>@lang('messages.action')</th>
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

@include('stock_transfer.partials.update_status_modal')

<section id="receipt_section" class="print_section"></section>

<!-- /.content -->
@stop
@section('javascript')
	<script src="{{ asset('js/stock_transfer.js?v=' . 123) }}"></script>

    <script>
        $(document).ready(function () {
            $('#stock_transfer_table').removeClass('no-footer');

            //Date range as a button
            $('#sell_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    stock_transfer_table.ajax.reload();
                }
            );
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function (ev, picker) {
                $('#sell_list_filter_date_range').val('');
                stock_transfer_table.ajax.reload();
            });

            $(document).on('change', '#from_location_id,#to_location_id, #warehouse_status, #transfer_route, #status', function () {
                stock_transfer_table.ajax.reload();
            });
            $(document).on('ifChanged', '#exclude_warehouse,#modified_transfer', function () {
                stock_transfer_table.ajax.reload();
            });
        });

    </script>
@endsection