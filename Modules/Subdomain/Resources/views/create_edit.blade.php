<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                @if(empty($subdomain))
                    @lang( 'subdomain::lang.register_business' )
                @else
                    @lang("Update Subdomain <b><i>$subdomain->sub_domain</i></b> Credentials")
                @endif
            </h4>
        </div>

        <div class="modal-body">
            @if(empty($subdomain))
                {!! Form::open(['url' => action([\Modules\Subdomain\Http\Controllers\SubdomainController::class, 'store']), 'method' => 'post', 'id' => 'subdomain_form' ]) !!}
            @else
                {!! Form::open(['url' => action([\Modules\Subdomain\Http\Controllers\SubdomainController::class, 'update'], ['id' => $subdomain->id]), 'method' => 'put', 'id' => 'subdomain_form' ]) !!}
            @endif
            <div class="box box-solid">
                <h5 class="text-bold">{{ __('subdomain::lang.business_information') }}</h5>
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-floating mb-3">
                            {!! Form::text('business_name',$supplier->name ?? null, ['class' => 'form-control', 'required', 'placeholder' => __( 'subdomain::lang.business_name' ) ]); !!}
                            {!! Form::label('business_name', __( 'subdomain::lang.business_name' )) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-floating mb-3">
                            {!! Form::text('owner_name',$supplier->owner ?? null, ['class' => 'form-control', 'required', 'placeholder' => __( 'subdomain::lang.business_owner' ) ]); !!}
                            {!! Form::label('owner_name', __( 'subdomain::lang.business_owner' )) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-floating mb-3">
                            {!! Form::text('owner_username',$supplier->username ?? null, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.username' ) ]); !!}
                            {!! Form::label('owner_username', __( 'lang_v1.username' )) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-floating mb-3">
                            {!! Form::email('owner_email',$supplier->email ?? null, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.email_address' ) ]); !!}
                            {!! Form::label('owner_email', __( 'lang_v1.email_address' )) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-10">
                        <div class="form-floating mb-3">
                            {!! Form::text('owner_password',$supplier->password ?? null, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.password' ) ]); !!}
                            {!! Form::label('owner_password', __( 'lang_v1.password' )) !!}
                        </div>
                    </div>
                </div>

            </div>
            <div class="box box-solid">
                <h5 class="text-bold">{{ __('subdomain::lang.subdomain_database') }}</h5>
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-floating mb-3">
                            {!! Form::text('subdomain',$supplier->configuration->posting_url ?? null, ['class' => 'form-control', 'required', 'placeholder' => __( 'subdomain::lang.name_related' ) ]); !!}
                            {!! Form::label('subdomain', __( 'subdomain::lang.name_related' )) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-floating mb-3">
                            {!! Form::text('username',$supplier->configuration->configuration_url ?? null, ['class' => 'form-control', 'required', 'placeholder' => __( 'subdomain::lang.username' ) ]); !!}
                            {!! Form::label('username', __( 'subdomain::lang.username' )) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-floating mb-3">
                            {!! Form::text('password',$supplier->configuration->configuration_url ?? null, ['class' => 'form-control', 'required', 'placeholder' => __( 'subdomain::lang.password' ) ]); !!}
                            {!! Form::label('password', __( 'subdomain::lang.password' )) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-floating mb-3">
                            {!! Form::text('database',$supplier->configuration->configuration_url ?? null, ['class' => 'form-control', 'required', 'placeholder' => __( 'subdomain::lang.database' ) ]); !!}
                            {!! Form::label('database', __( 'subdomain::lang.database' )) !!}
                        </div>
                    </div>
                </div>
                <span class="text-danger text-bold">(**Make sure these database credentials are valid)</span>
            </div>
{{--            <div class="row">--}}

{{--                @if(empty($subdomain))--}}
{{--                    <div class="form-group col-md-12">--}}
{{--                        {!! Form::label('name', __( 'subdomain.name' ) . ':*') !!}--}}
{{--                        {!! Form::text('subdomain', null, ['class' => 'form-control', 'required']); !!}--}}
{{--                    </div>--}}
{{--                    <div class="form-group col-md-12">--}}
{{--                        {!! Form::label('name', __( 'subdomain.database' ) . ':*') !!}--}}
{{--                        {!! Form::text('database', null, ['class' => 'form-control', 'required']); !!}--}}
{{--                    </div>--}}
{{--                @else--}}
{{--                    <div class="form-group col-md-12">--}}
{{--                        {!! Form::label('name', __( 'subdomain.name' ) . ':*') !!}--}}
{{--                        {!! Form::text('subdomain', $subdomain->sub_domain, ['class' => 'form-control', 'readonly', 'disabled']); !!}--}}
{{--                    </div>--}}
{{--                    <div class="form-group col-md-12">--}}
{{--                        {!! Form::label('name', __( 'subdomain.database' ) . ':*') !!}--}}
{{--                        {!! Form::text('database', $subdomain->db_name, ['class' => 'form-control', 'readonly', 'disabled']); !!}--}}
{{--                    </div>--}}
{{--                @endif--}}
{{--                <div class="form-group col-md-12">--}}
{{--                    {!! Form::label('name', __( 'subdomain.username' ) . ':*') !!}--}}
{{--                    @if(empty($subdomain))--}}
{{--                        {!! Form::text('username', null, ['class' => 'form-control', 'required']); !!}--}}
{{--                    @else--}}
{{--                        {!! Form::text('username', json_decode($subdomain->db_connection)->username ?? "", ['class' => 'form-control', 'required']); !!}--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--                <div class="form-group col-md-12">--}}
{{--                    {!! Form::label('name', __( 'subdomain.password' ) . ':*') !!}--}}
{{--                    @if(empty($subdomain))--}}
{{--                        {!! Form::text('password', null, ['class' => 'form-control', 'required']); !!}--}}
{{--                    @else--}}
{{--                        {!! Form::text('password', json_decode($subdomain->db_connection)->password, ['class' => 'form-control', 'required']); !!}--}}
{{--                    @endif--}}
{{--                </div>--}}

{{--            </div>--}}
                <div class="col-md-12">
                    <button type="submit" id="check_conn_button" class="btn @if(!empty($subdomain)) btn-info @else btn-primary  @endif pull-left">
                        @if(empty($subdomain))
                            {{__('messages.save')}}
                        @else
                            {{__('messages.update')}}
                        @endif  <i class="fa fas fa-check"></i>
                    </button>
{{--                    <button type="button" class="btn btn-default float-left" data-dismiss="modal">@lang( 'messages.close' )</button>--}}

                </div>
        </div>

{{--        <div class="modal-footer">--}}
{{--            <button type="submit" class="btn btn-primary">--}}
{{--                @if(empty($subdomain))--}}
{{--                    @lang( 'messages.save' )--}}
{{--                @else--}}
{{--                    @lang( 'messages.update' )--}}
{{--                @endif--}}

{{--            </button>--}}
{{--            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>--}}
{{--        </div>--}}

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->