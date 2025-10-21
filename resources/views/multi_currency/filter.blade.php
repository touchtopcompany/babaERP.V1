@if(multiCurrencyEnabled() && $exch_count > 0)
        <div class="box box-solid @if(!isMobile()) mb-12 @endif" id="multi_currency_div">
            <div class="box-body pb-0">
                {!! Form::hidden('default_currency_id',  1, ['id' => 'default_currency_id']) !!}
                {!! Form::hidden('default_code',  session('currency')['code'], ['id' => 'default_code']) !!}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('currency_id', __('business.currency') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                        <i class="fas fa-money-bill-alt"></i>
                    </span>
                                {!! Form::select('foreign_currency_id', $currencies['info'],  session('currency')['id'], ['class' => 'form-control select2','placeholder' => __( 'business.default_currency' ),  'id' => 'foreign_currency_id'], $currencies['rates']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">

                        <div class="form-group">
                            {!! Form::label('exchange_rate', __( 'lang_v1.currency_exchange_rate' ) . ':') !!}
                            {!! Form::text('exchange_rate',  config('constants.currency_exchange_rate'), ['class' => 'form-control', 'id' => 'exchange_rate', 'placeholder' => __( 'lang_v1.currency_exchange_rate' ), ]); !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endif