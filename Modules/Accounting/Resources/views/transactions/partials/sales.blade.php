<div class="pos-tab-content active">
    <div class="row">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('sell_list_filter_location_id',  __('purchase.business_location') . ':') !!}
                    {!! Form::select('sell_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', count($business_locations) == 1 ? "" :'placeholder' => __('lang_v1.all')  ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
            </div>
        @endcomponent
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped" id="sell_table">
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('messages.date')</th>
                        <th>@lang('sale.invoice_no')</th>
                        <th>@lang('sale.customer_name')</th>
                        <th>@lang('lang_v1.contact_no')</th>
                        <th>@lang('sale.location')</th>
                        <th>@lang('sale.payment_status')</th>
                        <th>@lang('lang_v1.payment_method')</th>
                        <th>@lang('sale.total_amount')</th>
                        <th>@lang('sale.total_paid')</th>
                        <th>@lang('lang_v1.added_by')</th>
                        <th>@lang('sale.sell_note')</th>
                        <th>@lang('sale.staff_note')</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>