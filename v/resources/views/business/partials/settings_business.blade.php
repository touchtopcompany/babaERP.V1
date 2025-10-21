<div class="pos-tab-content active">
    <style>
        p.strategy {
            margin-left: 2.8rem;
            color:#11cdef;
            font-weight:bold;
            margin-top:1.2rem;
            margin-bottom: 1.2rem
        }
    </style>
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('name',__('business.business_name') . ':*') !!}
                {!! Form::text('name', $business->name, ['class' => 'form-control', 'required',
                'placeholder' => __('business.business_name')]); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('start_date', __('business.start_date') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    
                    {!! Form::text('start_date', @format_date($business->start_date), ['class' => 'form-control start-date-picker','placeholder' => __('business.start_date'), 'readonly']); !!}
                </div>
            </div>
        </div>
      
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('currency_id', __('business.currency') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-money-bill-alt"></i>
                    </span>
                    {!! Form::select('currency_id', $currencies, $business->currency_id, ['class' => 'form-control select2','placeholder' => __('business.currency'), 'required']); !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('currency_symbol_placement', __('lang_v1.currency_symbol_placement') . ':') !!}
                {!! Form::select('currency_symbol_placement', ['before' => __('lang_v1.before_amount'), 'after' => __('lang_v1.after_amount')], $business->currency_symbol_placement, ['class' => 'form-control select2', 'required']); !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('time_zone', __('business.time_zone') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-clock"></i>
                    </span>
                    {!! Form::select('time_zone', $timezone_list, $business->time_zone, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('transaction_edit_days', __('business.transaction_edit_days') . ':*') !!}
                @show_tooltip(__('tooltip.transaction_edit_days'))
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-edit"></i>
                    </span>
                    {!! Form::number('transaction_edit_days', $business->transaction_edit_days, ['class' => 'form-control','placeholder' => __('business.transaction_edit_days'), 'required']); !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('business_logo', __('business.upload_logo') . ':') !!}
                    {!! Form::file('business_logo', ['accept' => 'image/*']); !!}
                    <p class="help-block"><i> @lang('business.logo_help')</i></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('fy_start_month', __('business.fy_start_month') . ':') !!} @show_tooltip(__('tooltip.fy_start_month'))
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::select('fy_start_month', $months, $business->fy_start_month, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('accounting_method', __('business.accounting_method') . ':*') !!}
                @show_tooltip(__('tooltip.accounting_method'))
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calculator"></i>
                    </span>
                    {!! Form::select('accounting_method', $accounting_methods, $business->accounting_method, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('date_format', __('lang_v1.date_format') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::select('date_format', $date_formats, $business->date_format, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('time_format', __('lang_v1.time_format') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-clock"></i>
                    </span>
                    {!! Form::select('time_format', [12 => __('lang_v1.12_hour'), 24 => __('lang_v1.24_hour')], $business->time_format, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('currency_precision', __('lang_v1.currency_precision') . ':*') !!} @show_tooltip(__('lang_v1.currency_precision_help'))
                {!! Form::select('currency_precision', [0 =>0, 1=>1, 2=>2, 3=>3,4=>4], $business->currency_precision, ['class' => 'form-control select2', 'required']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('quantity_precision', __('lang_v1.quantity_precision') . ':*') !!} @show_tooltip(__('lang_v1.quantity_precision_help'))
                {!! Form::select('quantity_precision', [0 =>0, 1=>1, 2=>2, 3=>3,4=>4], $business->quantity_precision, ['class' => 'form-control select2', 'required']); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row well">
           
        <div class="col-sm-8 my-2">
            <div class="form-group">
                {!! Form::label('default_profit_percent', __('Pricing  & Profit Strategy')) !!} @show_tooltip(__('tooltip.pricing_profit_strategy'))
                <div class="checkbox">
                        <label>{!! Form::radio('pricing_profit_strategy', 'margin', !(isset($business->pricing_profit_strategy)) || $business->pricing_profit_strategy == 'margin', 
                            [ 'class' => 'input-icheck']); !!} {{ __( 'Profit Margin' ) }} 
                        </label>
                        <small> -- {{(__('Tells you what percentage of selling price is profit.'))}}</small>
                        <br/>
                        <p class="strategy">Profit Margin = ((Selling Price - Cost Price) / Selling Price ) * 100%</p>
                        <br/>
                    

                    </div>
                    <div class="checkbox">
                        <label>{!! Form::radio('pricing_profit_strategy', 'markup', isset($business->pricing_profit_strategy) && $business->pricing_profit_strategy == 'markup', 
                            [ 'class' => 'input-icheck']); !!} {{ __( 'Markup' ) }} 
                        </label>
                       <small> -- {{(__('Tells you what percentage is added to the costs of your products in order to make a profit.'))}}</small>
                       <br/>
                       <p class="strategy">Markup = ((Selling Price - Cost Price) / Cost Price ) * 100%</p>

                    </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('default_profit_percent', __('Default Margin') . '*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-plus-circle"></i>
                    </span>
                    {!! Form::text('default_profit_percent', @num_format($business->default_profit_percent), ['class' => 'form-control input_number']); !!}
                </div>
            </div>
        </div>
        </div>
        <div class="clearfix"></div>
        
    </div>
     {{-- code --}}
    <div class="row hide">
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('code_label_1', __('lang_v1.code_1_name') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::text('code_label_1', $business->code_label_1, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('code_1', __('lang_v1.code_1') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::text('code_1', $business->code_1, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('code_label_2', __('lang_v1.code_2_name') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::text('code_label_2', $business->code_label_2, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('code_2', __('lang_v1.code_2') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::text('code_2', $business->code_2, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row hide">
        <div class="col-sm-8">
            <div class="form-group">
                <label>
                    {!! Form::checkbox('common_settings[is_enabled_export]', true, !empty($common_settings['is_enabled_export']) ? true : false , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_export' ) }}
                </label>
            </div>
        </div>
    </div>
</div>

