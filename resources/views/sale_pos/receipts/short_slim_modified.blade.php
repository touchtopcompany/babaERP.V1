<!DOCTYPE html>
<!--<html lang="en">-->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- <link rel="stylesheet" href="style.css"> -->
    <title>Receipt-{{$receipt_details->invoice_no}}</title>
    <style>
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
              }
            body {
                width: 80mm; /* Enlarged print width */
                /*font-family: 'Courier New', Courier, monospace;*/
                /*width: 320px; !* Wider for on-screen *!*/
                margin: auto;
                padding: 8px;
                        max-width: 80mm;
                background: #fff;
            }


            .center {
                text-align: center;
            }

            .bold {
                font-weight: bold;
            }

            .line {
                border-top: 1px dashed #000;
                margin: 10px 0;
            }

            table {
                width: 100%;
                font-size: 15px;
                border-collapse: collapse;
                margin-top: 10px;
            }

            table th, table td {
                border: 1px solid #000;
                padding: 6px;
                vertical-align: top;
                text-align: left;
            }

            .footer {
                margin-top: 20px;
                text-align: center;
                font-style: italic;
                font-size: 13px;
            }

            table th, table td {
                padding: 6px;
            }

            .h5 {
                text-transform: uppercase;
            }

            .meta-info {
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                /*margin-bottom: 10px;*/
            }

            .right {
                text-align: right;
                width: 75%;
            }

            .left {
                float: left;
            }

        }
    </style>

</head>
<body>
<div>


    <div class="left">
        <div class="h4 bold">Sales Receipt</div>

        @if(!empty($receipt_details->display_name))
            <div class="h5 bold">{{ $receipt_details->business_name }}</div>
        @endif

        @if(!empty($receipt_details->address))
            <div class="h5">{{ 'PO BOX ' .$receipt_details->address }}</div>
        @endif

        @if(!empty($receipt_details->contact))
            <div class="h5">{{ __('PHONE:') . $receipt_details->contact }}</div>
        @endif

        @if(!empty($receipt_details->tax_info1))
            <div class="h5">{{ 'TIN :'  }}{{ $receipt_details->tax_info1 }}

                @if(!empty($receipt_details->tax_info2))
                    {{ 'VRN :' }} {{ $receipt_details->tax_info2 }}
                @endif
            </div>
        @endif
        @if(!empty($receipt_details->payments))
            <div class="h6">
                {{ 'Sold To ' }}
                @foreach($receipt_details->payments as $payment)

                    {{$payment['method']}} <br>
                @endforeach
            </div>
        @endif
    </div>

    <div class="meta-info">
        <div class="right">
            Date: {{Carbon::createFromTimestamp(strtotime($receipt_details->invoice_date))->format('d/m/Y')}}</div>
        <div class="right">Sale No. {{$receipt_details->invoice_no}}</div>
    </div>

<div class="left">
    <div class="line"></div>
    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th>Qty</th>
            <th>U/M</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach($receipt_details->lines as $line)
            <tr>
                <td>
                    {{$line['name']}}
                </td>
                <td>
                    {{$line['quantity']}}
                </td>
                <td>
                    {{$line['base_unit_name']}}
                </td>
                <td>
                    {{$line['line_total']}}
                </td>
            </tr>
        @endforeach
        <tr>
            <td style="border: none"></td>
            <td colspan="3" style="text-align: right;" ><strong>Total</strong> {{$receipt_details->total}}</td>
        </tr>
        </tbody>
    </table>

    <div class="footer">THANKS FOR SHOPPING FROM US.... AHSANTE</div>
</div>
</div>
</body>
</html>
