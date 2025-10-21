<div class="pos-tab-content">
	<div class="row">
	@if(!empty($modules))
		<h4>@lang('lang_v1.enable_disable_modules')</h4>
		@foreach($modules as $k => $v)
            <div class="col-sm-4">
                <div class="form-group">
                    <div class="checkbox mt-0">
                    <br>
                      <label>
                        {!! Form::checkbox('enabled_modules[]', $k,  in_array($k, $enabled_modules) ,
                        ['class' => 'input-icheck']); !!} {{$v['name']}}
                      </label>
                      @if(!empty($v['tooltip'])) @show_tooltip($v['tooltip']) @endif
                    </div>
                </div>
            </div>
        @endforeach
            @php
                $mods = $package_mods;
            @endphp
            @foreach($mods as $mod)
                @if(isset($mod['n']))
                    {{-- Check if it is installed --}}
{{--                    @if(!(new \App\Utils\ModuleUtil())->isModuleInstalled($mod['n']))--}}
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="checkbox mt-0">
                                    <br>
                                    <label>
                                        {!! Form::checkbox('enabled_modules[]', Str::lower($mod['n']),  in_array( Str::lower($mod['n']), $enabled_modules) ,
                                        ['class' => 'input-icheck']); !!} {{$mod['n']}}
                                    </label>
                                    @show_tooltip($mod['d'])
                                </div>
                            </div>
                        </div>
{{--                    @endif--}}
                @endif
            @endforeach
        @endif
	</div>
</div>