@extends('layouts.app')
@section('title', __( 'sale.voided_product'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('sale.voided_product')
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('sell_list_filter_location_id',  __('purchase.business_location') . ':') !!}

                    {!! Form::select('sell_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('removed_by_id',  __('report.user') . ':') !!}
                    {!! Form::select('removed_by_id', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%','placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'sale.voided_product')])
            {{--            @can('sell.view')--}}
            <input type="hidden" name="is_direct_sale" id="is_direct_sale" value="0">
                <table class="table table-bordered table-striped ajax_view" id="removed_product_table">
                    <thead>
                    <tr>
                        <th>@lang('messages.date')</th>
                        <th>@lang('sale.location')</th>
                        <th>@lang('lang_v1.removed_by')</th>
                        <th>@lang('product.product_name')</th>
                        <th>@lang('lang_v1.total_items')</th>
                        <th>@lang('sale.total_amount')</th>

                    </tr>
                    </thead>
                    <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                        <td class="footer_items_total"></td>
                        <td class="footer_amount_removed"></td>
                    </tr>
                    </tfoot>
                </table>
        @endcomponent
    </section>
    <!-- /.content -->
    <div class="modal fade payment_modal" tabindex="-1" role="dialog"
         aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog"
         aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade register_details_modal" tabindex="-1" role="dialog"
         aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade close_register_modal" tabindex="-1" role="dialog"
         aria-labelledby="gridSystemModalLabel">
    </div>


@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function () {

            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function (ev, picker) {
                $('#sell_list_filter_date_range').val('');
                removed_product_table.ajax.reload();
            });

            $(document).on('change', '#sell_list_filter_location_id, #removed_by_id', function () {
                removed_product_table.ajax.reload();
            });


            //Date range as a button
            $('#sell_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    removed_product_table.ajax.reload();
                }
            );

            removed_product_table = $('#removed_product_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[1, 'desc']],
                // scrollY: "75vh",
                "ajax": {
                    "url": "/sells/get-void-product",
                    "data": function (d) {
                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }

                        if ($('#sell_list_filter_location_id').length) {
                            d.location_id = $('#sell_list_filter_location_id').val();
                        }
                        //
                        if ($('#removed_by_id').length) {
                            d.removed_by = $('#removed_by_id').val();
                        }
                        d = __datatable_ajax_callback(d);
                        //     }
                    },
                },
                columns: [
                    {data: 'removed_at', name: 'removed_at'},
                    {data: 'location_name', name: 'location_name'},
                    {data: 'full_name', name: 'full_name'},
                    {data: 'product_name', name: 'product_name'},
                    {data: 'quantity_removed', name: 'quantity_removed'},
                    {data: 'amount_removed', name: 'amount_removed'},
                ],
                footerCallback: function (row, data, start, end, display) {
                    var footer_items_total = 0;
                    var footer_amount_removed = 0;
                    for (var r in data) {
                        footer_items_total += parseFloat(data[r].quantity_removed);
                        footer_amount_removed += $(data[r].amount_removed).html() ? parseFloat($(data[r].amount_removed).data('orig-value')) : 0;
                    }

                    $('.footer_items_total').html('Items : ' + footer_items_total);
                    $('.footer_amount_removed').html(__currency_trans_from_en(footer_amount_removed));
                },
                createdRow: function (row, data, dataIndex) {
                    $(row).find('td:eq(6)').attr('class', 'clickable_td');
                }
            });


        });
    </script>
@endsection