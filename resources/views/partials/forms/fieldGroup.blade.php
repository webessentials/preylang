<div class="form-group">
    <label class="control-label">
        @lang($labelKey)
        @isset($addition)
            {{ ' (' . Lang::get('preylang.impact.field.villagerId') . ': ' }}
            <a href="{{ $villagerShowRoute }}" target="_blank"> {{ $villagerId }} </a>
            {{')'}}
        @endisset
    </label>
    @isset($canAdd)
        @if($canAdd)
        <a href="#" id="add-{{ $name }}" class="add-category @isset($canModify) {{ $canModify == true ? '' : 'hidden' }} @endisset float-right">
	        <span class="show-free-text badge">{{ Lang::get('preylang.showFreeText') }}</span>
	        <span class="hide-free-text badge badge-warning" style="display: none">{{ Lang::get('preylang.hideFreeText') }}</span>
        </a>
        @endif
    @endisset
    @switch($type)
        @case('text')
            <input type="text" class="form-control boxed" value="{{ $value }}" @if(isset($name)) name="{{ $name }}" @endif @if($readOnly) readonly @endif>
            @break
        @case('textarea')
            <textarea rows="2" class="form-control boxed" @if(isset($name)) name="{{ $name }}" @endif @if($readOnly) readonly @endif>{{ $value }}</textarea>
            @break
        @case('select')
            {!! Form::select($name, $list, $value, ['class' => isset($class) ? $class : 'form-control boxed']) !!}
            @isset($canAdd)
                @if($canAdd)
                    {!! Form::text('text-' . $name, '', ['class' => 'form-control category-free-text hidden', 'id' => 'text-' . $name]) !!}
                @endif
            @endisset
            @break
        @case('multiselect')
            {!! Form::select($name, $list, $value, ['class' => 'form-control boxed selectize', 'multiple' => 'true', 'id' => 'provinces-multiSelected']) !!}
            @break
        @case('datepicker')
            <input type="text" class="form-control datepicker boxed" value="{{ $value }}" autocomplete="off" @if(isset($name)) name="{{ $name }}" @endif >
            @break
        @case('datetimepicker')
            <input type="text" class="form-control datetimepicker boxed" value="{{ $value }}" @if(isset($name)) name="{{ $name }}" @endif @if($readOnly) readonly @endif>
            @break
        @case('number')
            <input type="number" class="form-control boxed" value="{{ $value }}" @if(isset($name)) name="{{ $name }}" @endif @if($readOnly) readonly @endif @if(isset($min)) min="{{ $min }}" @endif>
            @break
        @default
            <input type="text" class="form-control boxed" value="{{ $value }}" @if(isset($name)) name="{{ $name }}" @endif >
            @break
    @endswitch
</div>
