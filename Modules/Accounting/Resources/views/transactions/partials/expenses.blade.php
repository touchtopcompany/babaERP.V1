<div class="pos-tab-content">
    <div class="row">
        @component('components.filters', ['title' => __('report.filters')])
            @if(auth()->user()->can('all_expense.access'))
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', count($business_locations) == 1 ? "" :'placeholder' => __('lang_v1.all')  ]); !!}
                        {{--                            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}--}}
                    </div>
                </div>

{{--                <div class="col-sm-3">--}}
{{--                    <div class="form-group">--}}
{{--                        {!! Form::label('expense_for', __('expense.expense_for').':') !!}--}}
{{--                        {!! Form::select('expense_for', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-md-3">--}}
{{--                    <div class="form-group">--}}
{{--                        {!! Form::label('expense_contact_filter',  __('contact.contact') . ':') !!}--}}
{{--                        {!! Form::select('expense_contact_filter', $contacts, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}--}}
{{--                    </div>--}}
{{--                </div>--}}
            @endif
{{--            <div class="col-md-3">--}}
{{--                <div class="form-group">--}}
{{--                    {!! Form::label('expense_category_id',__('expense.expense_category').':') !!}--}}
{{--                    {!! Form::select('expense_category_id', $categories, null, ['placeholder' =>--}}
{{--                    __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'expense_category_id']); !!}--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="col-md-3">--}}
{{--                <div class="form-group">--}}
{{--                    {!! Form::label('expense_sub_category_id_filter',__('product.sub_category').':') !!}--}}
{{--                    {!! Form::select('expense_sub_category_id_filter', $sub_categories, null, ['placeholder' =>--}}
{{--                    __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'expense_sub_category_id_filter']); !!}--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('expense_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'expense_date_range', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('expense_payment_status',  __('purchase.payment_status') . ':') !!}
                    {!! Form::select('expense_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
        @endcomponent
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped" id="transaction_expense_table">
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('messages.date')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('lang_v1.recur_details')</th>
                        <th>@lang('expense.expense_category')</th>
                        <th>@lang('product.sub_category')</th>
                        <th>@lang('business.location')</th>
                        <th>@lang('sale.payment_status')</th>
                        <th>@lang('product.tax')</th>
                        <th>@lang('sale.total_amount')</th>
                        <th>@lang('purchase.payment_due')
                        <th>@lang('expense.expense_for')</th>
                        <th>@lang('contact.contact')</th>
                        <th>@lang('expense.expense_note')</th>
                        <th>@lang('lang_v1.added_by')</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>