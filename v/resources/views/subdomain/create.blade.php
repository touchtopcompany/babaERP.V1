<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action([\App\Http\Controllers\SubdomainController::class, 'postSubDomain']), 'method' => 'post', 'id' => 'subdomain
_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'subdomain.new_subdomain' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="form-group col-md-12">
                    {!! Form::label('name', __( 'subdomain.name' ) . ':*') !!}
                    {!! Form::text('SUBDOMAIN', null, ['class' => 'form-control', 'required']); !!}
                </div>

                <div class="form-group col-md-12">
                    {!! Form::label('name', __( 'subdomain.database' ) . ':*') !!}
                    {!! Form::text('DB_DATABASE', null, ['class' => 'form-control', 'required']); !!}
                </div>
                <div class="form-group col-md-12">
                    {!! Form::label('name', __( 'subdomain.username' ) . ':*') !!}
                    {!! Form::text('DB_USERNAME', null, ['class' => 'form-control', 'required']); !!}
                </div>
                <div class="form-group col-md-12">
                    {!! Form::label('name', __( 'subdomain.password' ) . ':*') !!}
                    {!! Form::text('DB_PASSWORD', null, ['class' => 'form-control', 'required']); !!}
                </div>

            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.add' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->