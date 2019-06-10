<div class="form-group">
    <label class="control-label">@lang($labelKey)</label>
    @switch($type)
        @case('text')
            <input type="text" class="form-control boxed" value="{{ $value }}" @if($readOnly) readonly="readonly" @endif>
            @break
        @case('select')
            {!! Form::select('subcategory', $subcategories, $value, ['id'=>'selectDistrict', 'class' => 'apply-select2 form-control']) !!}
            @break
        @case(3)
            <span class="bg-danger text-white p-2">{{ $label }}</span>
            @break
        @default
            <input type="text" class="form-control boxed" value="{{ $impact->villager->device_imei }}">
            @break
    @endswitch
</div>
