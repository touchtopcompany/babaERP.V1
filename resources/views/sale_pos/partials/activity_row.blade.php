@php
    $changes = $activity->changes;
    $attributes = $changes['attributes'] ?? null;
    $old = $changes['old'] ?? null;
    $status = $attributes['status'] ?? '';
    $payment_status = $attributes['payment_status'] ?? '';
    $sub_status = $attributes['sub_status'] ?? '';
    $shipping_status = $attributes['shipping_status'] ?? '';
    $status = in_array($sub_status, ['quotation', 'proforma']) ? $sub_status : $status;
    $final_total = $attributes['final_total'] ?? 0;

    $old_status = $old['status'] ?? '';
    $old_sub_status = $old['sub_status'] ?? '';
    $old_shipping_status = $old['shipping_status'] ?? '';
    $old_status = in_array($old_sub_status, ['quotation', 'proforma']) ? $old_sub_status : $old_status;
    $old_final_total = $old['final_total'] ?? 0;
    $old_payment_status = $old['payment_status'] ?? '';
    $update_note = $activity->getExtraProperty('update_note');

    //Description
    $description = $activity->description;
@endphp
<table class="no-border table table-slim mb-0">
    @if(!empty($activity->description == 'update_stock'))
        <style>
            .format-disp {
                font-size: 1.2rem;
                margin-right: 0.5rem;
                display: inline-block;
            }
        </style>
        <tr>
            <td class="width-50 text-left format-disp">
                <span >@lang('product.sku')</span>
            </td>
            <td ><span class="label bg-info">
                    {{ $activity->getExtraProperty('code') }}
                </span> </td>
        </tr>
        <tr>
            <td class="width-50 text-left format-disp">
                <span>@lang('category.category')</span>
            </td>
            <td><span class="label bg-info">
                         {{ ucwords($activity->getExtraProperty('category')) }}
                    </span></td>
        </tr>
        <tr>
            <td class="width-50 text-left format-disp">
                <span>@lang('lang_v1.stock_status')</span>
            </td>
            <td>
                <span class="label bg-info">
                     {{($activity->getExtraProperty('previous_stock')) .' &#8594; '. ($activity->getExtraProperty('current_stock'))}}
                </span>
            </td>
        </tr>
        <tr>
            <td class="width-50 text-left format-disp">
                <span>@lang('business.location')</span>
            </td>
            <td>
                <span class="label bg-info">
                     {{($activity->getExtraProperty('location')) ?? "Was not recorded"}}
                </span>
            </td>
        </tr>
    @endif
    @if(!empty($activity->description == 'edited') && $activity->subject_type != \App\VariationGroupPrice::class)
        <style>
            .format-disp {
                font-size: 1.1rem;
                margin-right: 0.5rem;
                display: inline-block;
            }
        </style>
        <tr>
            <td class="width-50 text-left">
                <span class="label bg-info">@lang('lang_v1.purchase_price')</span>
                <span class="format-disp">
               @format_currency($activity->getExtraProperty('old_purchase_inc_price')) -->  @format_currency($activity->getExtraProperty('new_purchase_inc_price'))
            </span>
                </td>
        </tr>
        <tr>
            <td class="width-50 text-left">
                <span class="label bg-info">@lang('lang_v1.selling_price')</span>
                <span class="format-disp">
                     @format_currency($activity->getExtraProperty('old_sell_inc_price')) -->  @format_currency($activity->getExtraProperty('new_sell_inc_price'))
                </span>
               </td>
        </tr>
    @endif
        @if(!empty($activity->description == 'edited') && $activity->subject_type == \App\VariationGroupPrice::class)
            <tr>
                <th class="width-50 text-left">
                    <span>@lang('lang_v1.product')</span>
                </th>
                <td>
                    {{ ucwords($activity->getExtraProperty('product')) }}
                </td>
            </tr>
            <tr>
                <th class="width-50 text-left">
                    <span class="label bg-info">@lang('lang_v1.purchase_price')</span>
                </th>
                <td>
                    @format_currency($activity->getExtraProperty('purchase_price'))
                </td>
            </tr>
            <tr>
                <th class="width-50 text-left">
                    <span class="label bg-info">@lang('lang_v1.selling_price')</span>
                </th>
                <td>
                    @format_currency($activity->getExtraProperty('selling_price'))
                </td>
            </tr>
        @endif
        @if(!empty($activity->description == 'opening_stock'))
            <tr>
                <td class="width-50 text-left">
                    <span >@lang('lang_v1.add_edit_opening_stock')</span>
                </td>
                <td ><span class="label bg-info">
                    {{ $activity->getExtraProperty('opening_stock') }}
                </span> </td>
            </tr>
            <tr>
                <td class="width-50 text-left">
                    <span>@lang('lang_v1.business_location')</span>
                </td>
                <td><span class="label bg-info">
                         {{ ucwords($activity->getExtraProperty('location')) }}
                    </span></td>
            </tr>
        @endif
@if(!empty($status) && $status != $old_status)
    <tr>
        <th class="width-50">@lang('sale.status'): </th> 
        <td class="width-50 text-left">
            @if(!empty($old_status))
                <span class="label bg-info">{{$statuses[$old_status] ?? ''}}</span> --> 
            @endif
            <span class="label bg-info">{{$statuses[$status] ?? ''}}</span>
         </td>
    </tr>
@endif

@if(!empty($shipping_status) && $shipping_status != $old_shipping_status)
    <tr>
        <th class="width-50">@lang('lang_v1.shipping_status'): </th> 
        <td class="width-50 text-left">
            @if(!empty($old_shipping_status))
                <span class="label bg-info">{{$shipping_statuses[$old_shipping_status] ?? ''}}</span> -->
            @endif
            <span class="label bg-info">{{$shipping_statuses[$shipping_status] ?? ''}}</span>
        </td>
     </tr>
@endif

@if(!empty($final_total) && $final_total != $old_final_total)
    <tr>
    <th class="width-50">@lang('sale.total'): </th> 
    <td class="width-50 text-left">
        @if(!empty($old_final_total))
            <span class="label bg-info">@format_currency($old_final_total)</span> --> 
        @endif
         <span class="label bg-info">@format_currency($final_total)</span>
     </td>
    </tr>
@endif

@if(!empty($payment_status) && $payment_status != $old_payment_status)
    <tr>
        <th class="width-50">@lang('sale.payment_status'): </th> 
        <td class="width-50 text-left">
            @if(!empty($old_payment_status))
                <span class="label bg-info">@lang('lang_v1.' . $old_payment_status)</span> --> 
            @endif
                <span class="label bg-info">@lang('lang_v1.' . $payment_status)</span>
        </td>
    </tr>
@endif

@if(!empty($update_note))
    @if(!is_array($update_note))
        <tr><td colspan="2">{{$update_note}}</td></tr>
    @endif
@endif
@if(!empty($activity->getExtraProperty('from')) && !empty($activity->getExtraProperty('to')))
    <tr>
        <td colspan="2">
            @if($activity->getExtraProperty('from') != 'completed')
                <span class="label {{$status_color_in_activity[$activity->getExtraProperty('from')]['class']}}" >
                    {{$status_color_in_activity[$activity->getExtraProperty('from')]['label']}}
                </span>
            @else
                <span class="label {{$status_color_in_activity[$activity->getExtraProperty('from')]['class']}}" >
                    {{$status_color_in_activity[$activity->getExtraProperty('from')]['label']}}
                </span>
            @endif
                &nbsp; -->
            @if($activity->getExtraProperty('to') != 'completed')
                <span class="label {{$status_color_in_activity[$activity->getExtraProperty('to')]['class']}}" >
                    {{$status_color_in_activity[$activity->getExtraProperty('to')]['label']}}
                </span>
            @else
                <span class="label {{$status_color_in_activity[$activity->getExtraProperty('to')]['class']}}" >
                    {{$status_color_in_activity[$activity->getExtraProperty('to')]['label']}}
                </span>
            @endif
        </td>
    </tr>
@endif
    @if(!empty($description))
        @if($description == 'product_edited')
            @php
            $product_arr = $activity->getExtraProperty('product');
            @endphp
            @foreach($product_arr as $product)
                {{-- Get product id  --}}
                @php
                    $get_product = \App\Variation::query()
                ->join('products as P', 'P.id', 'variations.product_id')
                ->where([
                'product_id' => $product['product_id'],
                'variations.id' => $product['variation_id'],
                ])->select('P.name as product')->first();
                $name = $get_product['product']
                @endphp
               <p>{{ $name }} <span class="label bg-info">{{$product['old_quantity']}}</span> -->
                   <span class="label bg-info">{{$product['new_quantity']}}</span></p>
            @endforeach
        @endif
    @endif
</table>