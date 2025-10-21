@extends('layouts.app')
@section('title', __('lang_v1.stock_taking'))

@section('content')
    <!-- Main content -->
    <section class="content">
        @if (session('notification') || !empty($notification))
            <div class="row">
                <div class="col-sm-12">
                    <div class="fa-1x alert @if(!session('notification.success')) alert-danger @else alert-success @endif  alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        @if(!session('notification.success') && session('notification.msg'))
                            {{ session('notification.msg') }}
                        @endif
                        @if(session('notification.success') && session('notification.msg'))
                            <p class="mt-5"> ***Stock was successfully imported!</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                @component('components.widget', ['class' => 'box-primary'])
                    {!! Form::open(['url' => action([\App\Http\Controllers\StockTakingController::class,'downloadInventoryTemplate']), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
                    <h4>@lang('stock_taking.download_inventory_template')</h4>
                    <div class="col-md-3">
                        @php
                            $default_location = null;
                            if(count($business_locations) == 1){
                              $default_location = array_key_first($business_locations->toArray());
                            }

                        @endphp
                        <div class="form-group">
                            {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                            {!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2','placeholder' => __('messages.please_select'), 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('stock_status_id', __('product.stock_status') . ':') !!}
                            {!! Form::select('stock_status_id', array('negative' => 'Negative','positive' => 'Positive','pos_neg' => 'Positive & Negative','zero' => 'Zero'), null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'stock_status_id', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('category_id', __('product.category') . ':') !!}
                            {!! Form::select('category_id[]', $categories, null, ['class' => 'form-control select2','multiple', 'style' => 'width:100%', 'id' => 'product_list_filter_category_id']); !!}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <br/>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-download"></i>
                            @lang('lang_v1.download')</button>
                    </div>
                    {!! Form::close() !!}
                @endcomponent
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @component('components.widget', ['class' => 'box-primary'])
                    {!! Form::open(['url' => action([\App\Http\Controllers\StockTakingController::class, 'postImportInventory']), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
                    <div class="row">
                        <div class="col-md-8">
                            <h4>@lang('stock_taking.import_inventory')</h4>
                            <div class="col-md-6">
{{--                                <div class="form-group">--}}
                                    {!! Form::file('inventory_csv', ['accept'=> '.xls,.xlsx', 'id' => 'customFile', 'class' => 'form-contro', 'required' => 'required']); !!}
{{--                                </div>--}}
                            </div>
                            <div class="col-md-3">
{{--                                <div class="form-group">--}}
                                    <div class="">
                                        <label>
                                            {!! Form::checkbox('print_stock_report', 1, false,
                                            [ 'class' => 'input-icheck', 'id' => 'print_stock_report']); !!} {{ __('Export Stock Report') }}
                                        </label>
                                    </div>
{{--                                </div>--}}
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary" id="import">@lang('messages.import')</button>
                            </div>

                        </div>
                    </div>

                    {!! Form::close() !!}
                    <br><br>
                @endcomponent
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.instructions')])
                    <strong>@lang('lang_v1.instruction_line1')</strong><br>
                    @lang('lang_v1.instruction_line2')
                    <br><br>
                    <table class="table table-striped">
                        <tr>
                            <th>@lang('lang_v1.col_no')</th>
                            <th>@lang('lang_v1.col_name')</th>
                            <th>@lang('lang_v1.instruction')</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>@lang('stock_taking.product_sku') <small class="text-muted">(@lang('lang_v1.required')
                                    )</small>
                            </td>
                            <td>&nbsp;@lang('stock_taking.sku_validity')</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>@lang('stock_taking.product_name') <small class="text-muted">(@lang('lang_v1.optional')
                                    )</small>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>@lang('product.category') <small class="text-muted">(@lang('lang_v1.optional')
                                    )</small>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>@lang('lang_v1.product_unit') <small class="text-muted">(@lang('lang_v1.optional')
                                    )</small>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>@lang('stock_taking.current_stock') <small class="text-muted">(@lang('lang_v1.required')
                                    )</small>
                            </td>
                            <td>&nbsp;</td>
                        </tr>

                        <tr>
                            <td>6</td>
                            <td>@lang('sale.unit_price') <small class="text-muted">(@lang('lang_v1.required')
                                    )</small>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>@lang('stock_taking.counted_products') <small
                                        class="text-muted">(@lang('lang_v1.required'))</small>
                            </td>
                            <td>&nbsp;@lang('stock_taking.total_counted')</td>
                        </tr>
                        <tr>
                            <td>8</td>
                            <td>@lang('business.location') <small class="text-muted">(@lang('lang_v1.required')
                                    )</small></td>
                            <td></td>
                        </tr>
                    </table>
                @endcomponent
            </div>
        </div>
    </section>
    <!-- /.content -->

@endsection
@section('javascript')
    <script>
        $(document).on('change', '#location_id ,#stock_status_id, #product_list_filter_category_id', function () {
            $('.btn.btn-success').removeAttr('disabled')
        });

        $(document).on('click', '#customFile', function () {
            $('#import').removeAttr('disabled')
        })
    </script>
@endsection