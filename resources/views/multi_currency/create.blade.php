<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\MultiCurrencyController::class, 'store']), 'method' => 'post', 'id' => 'multi_currency_setting_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.new_exchange_rate' )</h4>
    </div>

    <div class="modal-body">
        {!! Form::hidden('default_currency_id',  $business->currency_id, ['id' => 'default_currency_id']) !!}
        <div class="form-group">
          {!! Form::label('currency_id', __('business.currency') . ':') !!}
          <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-money-bill-alt"></i>
                    </span>
            {!! Form::select('currency_id', $currencies, $business->currency_id, ['class' => 'form-control select2','placeholder' => __('business.currency'), 'required']); !!}
          </div>
        </div>

      <div class="form-group">
        {!! Form::label('exchange_rate', __( 'lang_v1.currency_exchange_rate' ) . ':') !!}
          {!! Form::text('exchange_rate', 1, ['class' => 'form-control', 'id' => 'exchange_rate_id', 'placeholder' => __( 'lang_v1.currency_exchange_rate' )]); !!}
      </div>
        <div class="col-sm-12">
        <div class="form-group">
            <div class="checkbox">
                {!! Form::checkbox('dynamic_exchange_rate', 1, !empty($common_settings['enable_multi_currency']) , [ 'class' => 'input-icheck', 'id' => 'dynamic_exchange_rate']); !!} {{ __( 'lang_v1.dynamic_exchange_rate' ) }}
            </div>
        </div>
        </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->