<body>
<style>
    @page { size: A4; margin: 18mm 14mm; }

    h2 { margin: 0 0 6mm 0; font-size: 16pt; }
    .meta { font-size: 12pt; margin-bottom: 6mm; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #444; padding:6px 8px;font-size: 12pt }
    .center {text-align: center;}
</style>
 <link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
<div class="col-md-12 col-sm-12 width-100 align-right">
    <p class="text-right align-right">
        <strong>
            {{$meta['business']->name}}
        </strong>
        <br>
          {!! $meta['business']->business_address !!}

    </p>
</div>
<h3>{{ $meta['title'] }}</h3>

<div class="meta">
    {{ __('report.date_range') }}: {{ $meta['date_range'] }}<br>
    {{ __('lang_v1.no_of_products') }}: {{ $meta['limit'] }}<br>
    {{ __('Generated at') }}: {{ @format_datetime($meta['generated_at']) }}
</div>

<table>
    <thead>
    <tr>
        <th style="width:6%">#</th>
        <th>Product name/model</th>
        <th style="width:15%">Units sold</th>
        <th style="width:25%">Revenues generated</th>
    </tr>
    </thead>
    <tbody>
        @php
    $total_revenue = 0;
     @endphp
    @forelse ($rows as $i => $r)
            @php $total_revenue += $r['revenue'] @endphp
    <tr>
        <td class="right">{{ $i+1 }}</td>
        <td>{{ $r['name'] }}</td>
        <td class="right">{{ number_format($r['units']) }}</td>
        <td class="right">{{ number_format($r['revenue'], 2) }}</td>
    </tr>
    @empty
    <tr><td colspan="4" style="text-align:center">No data for selected filters</td></tr>
    @endforelse
    </tbody>
    <tr>
    <td colspan="3" class="center"><strong>TOTAL REVENUE </strong></td>
 <td class="right"><strong> @format_currency($total_revenue) </strong></td>
</tr>
</tfoot>
</table>
</body>
