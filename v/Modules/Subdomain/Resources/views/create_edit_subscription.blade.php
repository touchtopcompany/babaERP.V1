<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => route('add-subscription'), 'method' => 'post', 'id' => 'subdomain_add_subscription_form' ]) !!}

        {!! Form::hidden('subdomain_id', $subdomain_id); !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'superadmin::lang.add_subscription' )</h4>
        </div>

        <div class="modal-body">
            <div class="form-group mb-2">
                {!! Form::label('subscription_duration', __( 'subdomain::lang.subscription_duration' )) !!}
                <br/>
                {!! Form::number('subscription_duration', 'subscription_duration', ['class' => 'form-control width-40 pull-left', 'placeholder' => __('subdomain::lang.subscription_duration'), 'required' => true]); !!}

                {!! Form::select('subscription_duration_type',
                    [
                        'months' => __('lang_v1.months'),
                        'years' => __('lang_v1.years')],
                        'subscription_duration_type',
                    ['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select'), 'required' => true]); !!}
            </div>
            <br/>
            <br/>
            <div class="form-group">
                {!! Form::label('subscription_amount', __( 'subdomain::lang.subscription_amount' )) !!}
                {!! Form::number('subscription_amount', config('subdomain.subscription_amount'), ['class' => 'form-control', 'placeholder' => __( 'subdomain::lang.subscription_amount' ), 'required' => true ]); !!}
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->