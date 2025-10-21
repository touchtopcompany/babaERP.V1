@extends('layouts.app')
@section('title', __('lang_v1.multi_currency_settings'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang( 'lang_v1.multi_currency_settings' )
        </h1>
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

        @component('components.widget', ['class' => 'box-primary','help_text' => __('lang_v1.multi_currency_help_text')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{action([\App\Http\Controllers\MultiCurrencyController::class, 'create'])}}"
                            data-container=".view_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="multi_currency_table">
                    <thead>
                    <tr>
                        <th>@lang( 'business.currency' )</th>
                        <th>@lang( 'lang_v1.currency_exchange_rate' )</th>
                        <th>@lang( 'lang_v1.currency_exchange_rate_type' )</th>
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                    </thead>
                </table>
            </div>
        @endcomponent

        <div class="modal fade brands_modal" tabindex="-1" role="dialog"
             aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->
@stop
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function () {

            //multi_currency_table
            var multi_currency_table = $('#multi_currency_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('multi-currency-settings') }}",
                columnDefs: [{
                    "targets": 2,
                    "orderable": false,
                    "searchable": false
                }],
                columns: [
                    {data: 'currency', name: 'currency'},
                    {data: 'exchange_rate', name: 'exchange_rate'},
                    {data: 'exchange_rate_type', name: 'exchange_rate_type'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            $(document).on('shown.bs.modal', '.view_modal', function () {

                let curr_el = $('#currency_id'),
                    dyn_exchange = $('#dynamic_exchange_rate'),
                    exchange_rate = $('#exchange_rate_id');

                curr_el.on('change', function () {
                    if (dyn_exchange.is(":checked") === true) {
                        //Toggling checkbox
                        // dyn_exchange.attr("checked", dyn_exchange.attr("checked"))

                        /*Check uncheck
                        * // Check #x
                        $( "#x" ).prop( "checked", true );

                        // Uncheck #x
                        $( "#x" ).prop( "checked", false );
                        * */
                        dyn_exchange.prop("checked", false);
                    }
                })
                $(document).on('click',
                    '#dynamic_exchange_rate',
                    function () {
                        let checked = $(this).is(":checked");

                        checked ? exchange_rate.attr('readonly', true) : exchange_rate.removeAttr('readonly');

                        // exchange_rate.val('')

                        let default_curr = $('#default_currency_id').val(),
                            selected_curr = curr_el.val();

                        if (checked && default_curr !== selected_curr) {

                            //Get conversion api
                            $.ajax({
                                url: "{{ route('conversion-exchange-rate-api') }}",
                                dataType: 'json',
                                data: {
                                    'selected_currency': selected_curr,
                                }
                            }).done(function (res) {
                                exchange_rate.val(res.exchange_rate)
                            });
                        }
                    })
            });

            $(document).on('submit', 'form#multi_currency_setting_form', function (e) {
                e.preventDefault();
                postAjaxRequest($(this), multi_currency_table);
            });

            $(document).on('click', 'button.delete_multi_button', function () {
                updateAjaxRequest($(this), multi_currency_table, LANG.sure, 'DELETE');
            });

        });
    </script>
@endsection
