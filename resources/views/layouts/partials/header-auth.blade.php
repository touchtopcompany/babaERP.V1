@inject('request', 'Illuminate\Http\Request')

<div class="container-fluid">

	<!-- Language changer -->
	<div class="row">
	
		<div class="col-md-6">
			<div class="pull-right text-white">
	        @if(!($request->segment(1) == 'business' && $request->segment(2) == 'register') && $request->segment(1) != 'login')
		        	{{ __('business.already_registered')}} <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif">{{ __('business.sign_in') }}</a>
		        @endif
	        </div>
		</div>
	</div>
</div>