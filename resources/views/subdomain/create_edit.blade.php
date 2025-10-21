<div class="modal-dialog" role="document">
    <div class="modal-content">

        @if(empty($subdomain))
            {!! Form::open(['url' => action([\App\Http\Controllers\SubdomainController::class, 'postSubDomain']), 'method' => 'post', 'id' => 'subdomain
    _form' ]) !!}
        @else
            {!! Form::open(['url' => action([\App\Http\Controllers\SubdomainController::class, 'updateSubDomain'], ['id' => $subdomain->id]), 'method' => 'put', 'id' => 'subdomain
_form' ]) !!}
        @endif

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                @if(empty($subdomain))
                    @lang( 'subdomain.new_subdomain' )
                @else
                    @lang("Update Subdomain <b><i>$subdomain->sub_domain</i></b> Credentials")
                @endif
            </h4>
        </div>

        <div class="modal-body">
            <div class="row">
                @if(empty($subdomain))
                    <div class="form-group col-md-12">
                        {!! Form::label('name', __( 'subdomain.name' ) . ':*') !!}
                        {!! Form::text('SUBDOMAIN', null, ['class' => 'form-control', 'required']); !!}
                    </div>
                    <div class="form-group col-md-12">
                        {!! Form::label('name', __( 'subdomain.database' ) . ':*') !!}
                        {!! Form::text('DB_DATABASE', null, ['class' => 'form-control', 'required']); !!}
                    </div>
                @else
                    <div class="form-group col-md-12">
                        {!! Form::label('name', __( 'subdomain.name' ) . ':*') !!}
                        {!! Form::text('SUBDOMAIN', $subdomain->sub_domain, ['class' => 'form-control', 'readonly', 'disabled']); !!}
                    </div>
                    <div class="form-group col-md-12">
                        {!! Form::label('name', __( 'subdomain.database' ) . ':*') !!}
                        {!! Form::text('DB_DATABASE', $subdomain->db_name, ['class' => 'form-control', 'readonly', 'disabled']); !!}
                    </div>
                    @endif
                <div class="form-group col-md-12">
                    {!! Form::label('name', __( 'subdomain.username' ) . ':*') !!}
                    @if(empty($subdomain))
                        {!! Form::text('DB_USERNAME', null, ['class' => 'form-control', 'required']); !!}
                    @else
                        {!! Form::text('DB_USERNAME', json_decode($subdomain->db_connection)->username ?? "", ['class' => 'form-control', 'required']); !!}
                    @endif
                </div>
                <div class="form-group col-md-12">
                    {!! Form::label('name', __( 'subdomain.password' ) . ':*') !!}
                    @if(empty($subdomain))
                        {!! Form::text('DB_PASSWORD', null, ['class' => 'form-control', 'required']); !!}
                    @else
                        {!! Form::text('DB_PASSWORD', json_decode($subdomain->db_connection)->password, ['class' => 'form-control', 'required']); !!}
                    @endif
                </div>

            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">
                @if(empty($subdomain))
                    @lang( 'messages.add' )
                @else
                    @lang( 'messages.update' )
                @endif

            </button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->