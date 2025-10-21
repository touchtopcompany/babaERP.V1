@extends('layouts.app')
@section('title', __('lang_v1.add_selling_price_group_prices'))

@section('content')

	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>@lang('lang_v1.add_selling_price_group_prices')</h1>
	</section>

	<!-- Main content -->
	<section class="content">
		{!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'saveGroupPrices']), 'method' => 'post', 'id' => 'selling_price_form' ]) !!}
		{!! Form::hidden('product_id', $product->id); !!}
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-solid">
					<div class="box-header">
						<h3 class="box-title">@lang('sale.product'): {{$product->name}} ({{$product->sku}})</h3>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12">
								<div class="table-responsive">
									<table class="table table-condensed table-th-green table-bordered text-center table-striped">
										<thead>
										<tr>
											<th></th>
											<th>@lang('lang_v1.purchase_price')</th>
											<th>@lang('lang_v1.selling_price')</th>
										</tr>
										</thead>
										<tbody>
										@foreach($product->variations as $variation)
											<tr>
												<td>@lang('lang_v1.default') @lang('lang_v1.price')</td>
												<td>
													<span data-currency_symbol="true" class="display_currency">
														{{ $variation->dpp_inc_tax }}
													</span>
												</td>
												<td>
													<span data-currency_symbol="true" class="display_currency">
														{{ $variation->sell_price_inc_tax }}
													</span>
												</td>
											</tr>
										@endforeach
										<tr>
											<td colspan="3"></td>
										</tr>

										@foreach($product->variations as $variation)
											@foreach($price_groups as $price_group)
												<tr>
													<td>{{$price_group->name}}</td>
													<td>{!! Form::text('purchase_group_prices[' . $price_group->id . '][' . $variation->id . ']', !empty($variation_prices[$variation->id][$price_group->id]) ? @num_format($variation_prices[$variation->id][$price_group->id]) : 0, ['class' => 'form-control input_number input-sm'] ); !!}
													</td>
													<td>{!! Form::text('selling_group_prices[' . $price_group->id . '][' . $variation->id . ']', !empty($variation_prices[$variation->id2][$price_group->id]) ? @num_format($variation_prices[$variation->id2][$price_group->id]) : 0, ['class' => 'form-control input_number input-sm'] ); !!}
													</td>
												</tr>
											@endforeach
										@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				{!! Form::hidden('submit_type', 'save', ['id' => 'submit_type']); !!}
				<div class="text-center">
					<div class="btn-group">
						<a href="{{ route('products.index') }}"
						   class="btn bg-info margin-r-5 text-white">@lang('lang_v1.go_back')</a>
						@if($price_group_count > 1)
							<button id="opening_stock_button" @if($product->enable_stock == 0) disabled
									@endif type="submit"
									value="submit_n_add_opening_stock"
									class="btn bg-purple submit_form">@lang('lang_v1.save_n_add_opening_stock')</button>
							<button type="submit" value="save_n_add_another"
									class="btn bg-maroon submit_form">@lang('lang_v1.save_n_add_another')</button>
						@endif
						<button type="submit" value="submit"
								class="btn btn-primary submit_form">@lang('messages.save')</button>
					</div>
				</div>
			</div>
		</div>

		{!! Form::close() !!}
	</section>
@stop
@section('javascript')
	<script type="text/javascript">
		$(document).ready(function () {
			$('button.submit_form').click(function (e) {
				e.preventDefault();
				$('input#submit_type').val($(this).attr('value'));

				if ($("form#selling_price_form").valid()) {
					$("form#selling_price_form").submit();
				}
			});
		});
	</script>
@endsection
