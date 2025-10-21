@extends('layouts.app')
@section('title', __('subdomain.babaerp_subdomain'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h4>@lang( 'subdomain.babaerp_subdomain' ) @show_tooltip(__('subdomain.babaerp_subdomain_help'))
        </h4>
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary'])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{action([\App\Http\Controllers\SubdomainController::class, 'createSubDomain'])}}"
                            data-container=".registered_subdomain_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="sub_domain_table">
                    <thead>
                    <tr>
                        <th>@lang( 'subdomain.name' )</th>
                        <th>@lang( 'subdomain.database' )</th>
                        <th>@lang( 'subdomain.env_file' )</th>
                        <th>@lang( 'subdomain.registered_on' )</th>
                        <th>@lang( 'subdomain.active_modules' )</th>
                        <th>@lang( 'lang_v1.added_by' )</th>
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                    </thead>
                </table>
            </div>
        @endcomponent

        <div class="modal fade registered_subdomain_modal contains_select2" tabindex="-1" role="dialog"
             aria-labelledby="gridSystemModalLabel">
        </div>

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
                ajax: '/subdomain/registered',
                columnDefs: [{
                    "orderable": false,
                    "searchable": false
                }],
                columns: [
                    {data: 'sub_domain', name: 'sub_domain'},
                    {data: 'db_name', name: 'db_name'},
                    {data: 'env_file', name: 'env_file'},
                    {data: 'registered_on', name: 'registered_on'},
                    {data: 'modules', name: 'modules'},
                    {data: 'registered_by', name: 'registered_by'},
                    {data: 'action', name: 'action'},
                ]
            });

                       $(document).on('click', 'button.delete_sd_button', function () {
                swal({
                    title: LANG.sure,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var href = $(this).data('href');
                        var data = $(this).serialize();

                        $.ajax({
                            method: "DELETE",
                            url: href,
                            dataType: "json",
                            data: data,
                            success: function (result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                    sub_domain_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });


        });
    </script>
@endsection
