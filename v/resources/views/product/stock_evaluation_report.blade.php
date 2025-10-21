<div class="modal-dialog modal-xl" role="document" id="view_stock_evaluation">
    <div class="modal-content">
        <div class="modal-header" style="padding-top: 10px;padding-bottom: 0">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <h3 class="text-center modal-titl text-uppercase p-4" style="margin-top:0;font-weight: 600" id="modalTitle">
                {{ $business }}
            </h3>
        </div>
        <div class="modal-body" style="padding-top: 0">
            <h4 class="text-center text-uppercase" style="font-weight: 600;margin: 20px">
                @if($location_name === "ALL LOCATIONS") {{ $location_name }} BRANCHES @else {{ $location_name }} BRANCH @endif
            </h4>
            <h4 class="text-center text-upper mt-15" style="font-weight: 500">
                Stock Evaluation Department Analysis
            </h4>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-condensed table-responsive">
                        <thead>
                        <tr>
                            <th colspan="2">Department</th>
                            <th>Value @C.P</th>
                            <th>Value @S.P</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $categories = [];
                            $tot_pur= 0;
                            $tot_sel = 0;
                        @endphp

                        @foreach($report as $item)
                            @php
                                $tot_pur += $item->purchasing;
                                $tot_sel += $item->selling;
                            @endphp
                            <tr>
                                <td>D{{ $loop->iteration }}</td>
                                <td>{{ @ucwords(strtolower($item->category)) }}</td>
                                <td>{{ @number_format($item->purchasing) }}</td>
                                <td>{{ @number_format($item->selling) }}</td>
                            </tr>
                        @endforeach
                        <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th class="display_currency" data-currency_symbol ="true">{{ @number_format($tot_pur) }}</th>
                            <th class="display_currency" data-currency_symbol ="true">{{ number_format($tot_sel) }}</th>
                        </tr>
                        </thead>
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary no-print"
                    aria-label="Print"
                    onclick="$(this).closest('div.modal').printThis();">
                <i class="fa fa-print"></i> @lang( 'messages.print' )
            </button>
            <button type="button" class="btn btn-default no-print"
                    data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div>
</div>
