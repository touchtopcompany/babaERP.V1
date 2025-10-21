@extends('layouts.app')
@section('title', __('subdomain::lang.subdomain_accounts'))

@section('css')
    <link rel="stylesheet" href="{{ asset('modules/vfd/customed.css') }}">

    <style>
        td.fas, .fa-solid{
            font-size: 1.7rem;
        }

    </style>
@endsection
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h4>{{__('subdomain::lang.subdomain_accounts' )}} @show_tooltip(__('subdomain::lang.babaerp_subdomain_help'))
        </h4>
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary'])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{action([\Modules\Subdomain\Http\Controllers\SubdomainController::class, 'create'])}}"
                            data-container=".view_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="sub_domain_table">
                    <thead>
                    <tr>
                        <th>@lang( 'business.business_name' )</th>
                        <th>@lang( 'superadmin::lang.active_subscription' )</th>
                        <th>@lang( 'purchase.amount' )</th>
                        <th>@lang( 'subdomain::lang.last_active' )</th>
                        <th>@lang( 'subdomain::lang.account_status')</th>
                        <th>@lang( 'subdomain::lang.subdomain_name' )</th>
                        <th>@lang( 'subdomain::lang.active_modules' )</th>
{{--                        <th>@lang( 'lang_v1.added_by' )</th>--}}
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                    </thead>
                </table>
            </div>
        @endcomponent
    </section>
    <!-- /.content -->

@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function () {

            //sub_domain_table
            var sub_domain_table = $('#sub_domain_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('subdomains.index')}}",
                columnDefs: [{
                    "orderable": false,
                    "searchable": false
                }],
                columns: [
                    {data: 'business_name', name: 'business_name'},
                    {data: 'active_subscription', name: 'active_subscription'},
                    {data: 'package_price', name: 'package_price'},
                    {data: 'last_active', name: 'last_active'},
                    {data: 'account_status', name: 'account_status'},
                    {data: 'sub_domain', name: 'sub_domain'},
                    {data: 'modules', name: 'modules'},
                    {data: 'action', name: 'action'},
                ]
            });

            $(document).on('submit', 'form#subdomain_form', function(e){
                e.preventDefault();
                postAjaxRequest($(this), sub_domain_table);
            });

            $(document).on('submit', 'form#subdomain_add_subscription_form', function(e){
                e.preventDefault();
                postAjaxRequest($(this), sub_domain_table);
            });

            $(document).on('click', '.update_subdomain_btn', function (e) {
                e.preventDefault();
                updateAjaxRequest($(this), sub_domain_table, LANG.confirm_business_update);
            });
        });
    </script>
@endsection
