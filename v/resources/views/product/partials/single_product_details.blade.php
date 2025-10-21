<br>
<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table bg-gray">
				<tr class="bg-green">
					@can('view_purchase_price')
						<th>@lang('product.default_purchase_price') (@lang('product.exc_of_tax'))</th>
						<th>@lang('product.default_purchase_price') (@lang('product.inc_of_tax'))</th>
					@endcan
					@can('access_default_selling_price')
						@can('view_purchase_price')
				        	<th>@lang('product.profit_percent')</th>
				        @endcan
				        <th>@lang('product.default_selling_price') (@lang('product.exc_of_tax'))</th>
				        <th>@lang('product.default_selling_price') (@lang('product.inc_of_tax'))</th>
				    @endcan
				    @if(!empty($allowed_group_prices))
			        	<th>@lang('lang_v1.group_prices') - @lang('PP & SP')</th>
			        @endif
			        <th>@lang('lang_v1.variation_images')</th>
				</tr>
				@foreach($product->variations as $variation)
				<tr>
					@can('view_purchase_price')
					<td>
						<span class="display_currency" data-currency_symbol="true">{{ $variation->default_purchase_price }}</span>
					</td>
					<td>
						<span class="display_currency" data-currency_symbol="true">{{ $variation->dpp_inc_tax }}</span>
					</td>
					@endcan
					@can('access_default_selling_price')
						@can('view_purchase_price')
						<td>
							{{ @num_format($variation->profit_percent) }}
						</td>
						@endcan
						<td>
							<span class="display_currency" data-currency_symbol="true">{{ $variation->default_sell_price }}</span>
						</td>
						<td>
							<span class="display_currency" data-currency_symbol="true">{{ $variation->sell_price_inc_tax }}</span>
						</td>
					@endcan
					@if(!empty($allowed_group_prices))
			        	<td class="td-full-width">
							@if(count($allowed_group_prices) > 0)
								@foreach($allowed_group_prices as $key => $value)
									@if(!empty($group_price_details[$variation->id][$key]))
										@if($group_price_details[$variation->id][$key]['calculated_price'] > 0)
											<strong>{{$value}}</strong> -
											<span class="display_currency"
												  data-currency_symbol="true">{{ $group_price_details[$variation->id][$key]['price'] }}</span> &
											<span class="display_currency"
												  data-currency_symbol="true">{{ $group_price_details[$variation->id][$key]['calculated_price'] }}</span>
											<br>
										@endif
									@endif
								@endforeach
							@endif
			        	</td>
			        @endif
			        <td>
			        	@foreach($variation->media as $media)
			        		{!! $media->thumbnail([60, 60], 'img-thumbnail') !!}
			        	@endforeach
			        </td>
				</tr>
				@endforeach
			</table>
		</div>
	</div>
</div>