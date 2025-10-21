

<div class="modal-dialog modal-lg" role="document" id="view_stock_evaluation">
    <div class="modal-content">
        <div class="modal-header" style="padding-top: 10px;padding-bottom: 0">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <h4 class="text-center modal-titl text-uppercase p-4" style="margin-top:0;font-weight: 600" id="modalTitle">
                {{ $business }}
            </h4>
        </div>
        <div class="modal-body" style="padding-top: 0">
            <h5 class="text-center text-uppercase" style="font-weight: 600;margin: 20px">
                @if($location_name === "ALL LOCATIONS")
                    {{ $location_name }} BRANCHES
                @else
                    {{ $location_name }}
                @endif
            </h5>
            <h5 class="text-center text-upper mt-15" style="font-weight: 500">
                @if (!empty($output['tax_exist']))
                    {{ __('TOTAL ZED HISTORY SUMMARY')}}
                @else
                    {{ __('TOTAL SALES SUMMARY')}}
                @endif
            </h5>
            @if(!empty($dates['start']))
                <h6 class="text-center text-upper mt-15" style="font-weight: 600">
                    @if($dates['start'] == $dates['end'])
                        On : {{ $dates['start'] }}
                    @else
                        &nbsp;&nbsp;&nbsp; From : {{ $dates['start'] }}  &nbsp;&nbsp;To : {{ $dates['end'] }}
                    @endif
                </h6>
            @endif
            <div class="row">
                <div class="col-sm-12">
                    <style>
                        .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
                            padding: 6px 4px;
                        }

                        tr.b-top {
                            border-bottom: 1px solid #343a40 !important;
                        }
                    </style>
                    @php
                        $methods = array_unique($output['payment_types']);
                    @endphp
                    @if(!empty($output['due_invoice_payment']))
                    <table style="width: 60%"
                           class="table table-hover table-condensed table-striped table-no-top-cell-border table-responsive">
                        <tbody>
                        <tr><th colspan="4" class="text-center text-uppercase">@lang('Previous Invoice Payments')</th></tr>
                        <tr>
                            <th>{{ __('Payment Date') }}</th>
                            <th>{{ __('Invoice Date') }}</th>
                            <th>{{ __('Invoice No') }}</th>
                            <th>{{ __('Method') }}</th>
                            <th>{{ __('Amount') }}</th>
                        </tr>
                        @php
                            $tpi = 0;
                        @endphp

                        @foreach($output['due_invoice_payment'] as $invoice_payment)
                            <tr>
                                <td rowspan="{{ count($invoice_payment) + 1 }}">{{ Carbon::parse($invoice_payment[0]['paid_on'])->format('d-m-Y')  }}</td>

                            @foreach($invoice_payment as $payment)
                                @php $tpi +=  $payment['amount'] @endphp
                                <tr>
                                    <td>{{ Carbon::parse( $payment['t_date'])->format('d-m-Y') }}</td>
                                    <td>{{ $payment['invoice'] }}</td>
                                    <td>{{ $methods[$payment['method']] }}</td>
                                    <td>@format_currency($payment['amount'])</td>
                                </tr>
                                @endforeach
                                </tr>
                            @endforeach
                            <tr><th  colspan="4" class="text-center">Total</th><th  class='b-top b-bottom'>@format_currency($tpi)</th></tr>
                        </tbody>
                    </table>
                    <br>
                    @endif
                    <table style="width: 60%"
                           class="table table-hover table-condensed table-striped table-no-top-cell-border table-responsive">
                        <tbody>
                        @php
                            $total_cash = 0;
                            $total_payment = 0;
                            $cash_pp = 0;
                        @endphp
                        @foreach($output['purchase_payments'] as $purchase_payment)
                            @if($purchase_payment->method == 'cash')
                                @php
                                    $cash_pp = $purchase_payment->purchases_payment;
                                @endphp
                            @endif
                                @endforeach
                        @foreach($output['sells_payments'] as $sell_payment)
                            @if($sell_payment->method == 'cash')
                                @php
                                    $total_cash = $sell_payment->sell_payment;
                                @endphp
                            @endif
                            @php
                                $total_payment += $sell_payment->sell_payment;
                            @endphp
                            <tr>
                                <td>{{ $methods[$sell_payment->method] }}</td>
                                <td>
                                    @format_currency($sell_payment->sell_payment)
                                </td>
                            </tr>
                        @endforeach
                        <tr><td></td></tr>
                        <tr class="succes">
                            <th>Total Payment</th>
                            <th>@format_currency($total_payment)</th>
                        </tr>
                        <tr><td></td></tr>
                        <tr><td></td></tr>
                        <tr>
                            <td>{{ __('Invoice Due') }}</td>
                            <td>@format_currency($output['invoice_due'])</td>
                        </tr>
{{--                        <tr><td></td></tr>--}}
{{--                        <tr class="succes">--}}
{{--                            <th>{{ __('Total Sales') }}</th>--}}
{{--                            <th class='b-top b-bottom'>@format_currency($output['total_sell'])</th>--}}
{{--                        </tr>--}}

                        </tbody>
                    </table>
                    <br>
                    <table style="width: 50%"
                           class="table table-hover table-condensed table-striped table-no-top-cell-border table-responsive">
                        <tbody>
                        <tr>
                            <th>{{ \Illuminate\Support\Str::upper(__('lang_v1.sell_return')) }}</th>
                        </tr>
                        <tr>
                            <td>{{ __('Total Sell Return') }}</td>
                            <td>@format_currency($output['total_sell_return'])</td>
                        </tr>
                        <tr>
                            <td>{{ __('Total Sell Return Paid') }}</td>
                            <td>@format_currency($output['total_sell_return_paid'])</td>
                        </tr>
                        </tbody>
                    </table>
                    <table style="width: 60%"
                           class="table table-hover table-condensed table-striped table-no-top-cell-border table-responsive">

                        <tbody>.
                            <tr>
                                <th>{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::plural(__('lang_v1.expense'))) }}</th>
                            </tr>
                            <tr>
                                <td>{{ __('Total Expenses') }}</td>
                                <td>@format_currency($output['total_expense'])</td>
                            </tr>
                            <tr class="b-top b-bottom">
                                <td>{{ __('Paid Expenses') }}</td>
                                <td>@format_currency($output['total_expense_paid'])</td>
                            </tr>
                            <tr>
                                <th>{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::plural(__('lang_v1.purchase'))) }}</th>
                            </tr>
                            <tr>
                                <td>{{ __('Total Purchases') }}</td>
                                <td>@format_currency($output['total_purchase_inc_tax'])</td>
                            </tr>
                            <tr>
                                <td>{{ __('Paid Purchases') }}</td>
                                <td>@format_currency($output['total_purchase_inc_tax'] - $output['purchase_due'])</td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    @if (!empty($output['taxes']))
                        <table class="table table-no-side-cell-border" style="width: 60%">
                            <thead>
                            <tr>
                                <th colspan="4">Total VAT Summary</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th>Code</th>
                                <th>Percent</th>
                                <th>{{ __('Total Sale (Excluding VAT)')}}</th>
                                <th>VAT Amount</th>
                            </tr>
                            <tr>
                                <td>A</td>
                                <td>18</td>
                                <td>@format_currency($output['sells']->ttl_tax_exclusive)</td>
                                <td>@format_currency($output['sells']->ttl_tax_inclusive -
                                    $output['sells']->ttl_tax_exclusive)
                                </td>
                            </tr>
                            <tr>
                                <td>E</td>
                                <td>0.00</td>
                                <td>@format_currency($output['sells']->ttl_exempted)</td>
                                <td>@format_currency(0)</td>
                            </tr>
                            </tbody>
                        </table>
                    @endif
                    <table style="width: 60%"
                           class="table table-hover table-condensed table-striped table-no-top-cell-border table-responsive">

                        <tr class="success">
                            @php 
                            $cash_to_bank = 0;
                            $remaining = $total_cash - $output['total_expense_paid'] - $output['total_sell_return_paid'] - $cash_pp;
                            if($remaining > 0){
                            $cash_to_bank = $remaining;
                            }
                            @endphp
                            <th>{{ \Illuminate\Support\Str::upper(__('Cash To Bank')) }} @show_tooltip(__('Total Cash - (Expenses + Sell Return + Purchases) <br/> <b>NB: Only Paid in Cash</b>'))</th>
                            <th>@format_currency($cash_to_bank)</th>
                        </tr>
                    </table>
                    <p class="text-center">**** End of Report *****</p>
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
