@extends('layouts.app')
@section('title', __('lang_v1.update_product_price'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'lang_v1.update_product_price' )
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @if (session('notification') || !empty($notification))
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    @if(!empty($notification['msg']))
                        {{$notification['msg']}}
                    @elseif(session('notification.msg'))
                        {{ session('notification.msg') }}
                    @endif
                </div>
            </div>  
        </div>     
    @endif
        <div class="row">
            <div class="col-sm-12">
                @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.export_selling_price_group_prices')])
                    {!! Form::open(['url' => action([\App\Http\Controllers\PriceGroupController::class, 'export']), 'method' => 'post' ]) !!}
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('price_group_id', __('lang_v1.price_group') . ':') !!}
                                {!! Form::select('price_group_id', $price_groups_for_dropdown, null, ['class' => 'form-control select2', 'id' => 'filter_price_group_id','placeholder' => __('messages.please_select'),'required' => true]); !!}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('category_ids', __('product.category') . ':') !!}
                                {!! Form::select('category_id[]', $categories, null, ['class' => 'form-control select2', 'style' => 'width:100%','multiple', 'id' => 'filter_category_id']); !!}
                            </div>
                        </div>
                        <div class="col-sm-4" style="margin-top: 24px !important;">
                            <div class="form-group">
                                <button type="submit"
                                        class="btn btn-primary">@lang('messages.export')</button>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-sm-12" style="margin-left:15px">
                                <h4>@lang('lang_v1.instructions'):</h4>
                                <p>
                                    &bull; @lang('lang_v1.price_group_import_istruction')
                                </p>
                                <p>
                                    &bull; @lang('lang_v1.price_group_import_istruction1')
                                </p>
                                <p>
                                    &bull; @lang('lang_v1.price_group_import_istruction2')
                                </p>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                @endcomponent
            </div>
            <div class="col-sm-12">
                @component('components.widget', ['class' => 'box-primary'])
                    {!! Form::open(['url' =>action([\App\Http\Controllers\PriceGroupController::class, 'import']), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
                    <div class="row mb-10">
                        <div class="col-sm-6">
                            <h4>@lang('lang_v1.import_selling_price_group_prices')</h4>
                        </div>
                    </div>
                    <div class="row">
{{--                        <div class="col-sm-4">--}}
{{--                            <div class="form-group">--}}
{{--                                {!! Form::label('price_group_id', __('lang_v1.price_group') . ':') !!}--}}
{{--                                {!! Form::select('price_group_id', $price_groups_for_dropdown, null, ['class' => 'form-control select2', 'id' => 'filter_price_group_id','placeholder' => __('messages.please_select'),'required' => true]); !!}--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="col-sm-4"  style="margin-top: 24px !important;">
                            <div class="form-group">
                                {{--                                    {!! Form::label('email', 'E-Mail Address', ['class' => 'form-label', 'for' => 'customFile'])!!}--}}
                                {!! Form::file('product_group_prices', ['accept'=> '.xls,.xlsx', 'id' => 'customFile', 'class' => 'form-control', 'required' => 'required']); !!}
                            </div>
                        </div>
                        <div class="col-sm-4"  style="margin-top: 24px !important;">
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Did you confirm all the inputs if they are correct?');">@lang('messages.import')</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                @endcomponent
            </div>
        </div>

</section>
<!-- /.content -->
@stop
@section('javascript')

    <script>
        $(document).on('change', '#filter_category_id ,#filter_price_group_id', function () {
            $('.btn.btn-primary').removeAttr('disabled')
        });
    </script>
@stop
