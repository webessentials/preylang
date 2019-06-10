@if($readOnly)
<label class="control-label">{!! Form::checkbox((isset($name) ? $name : ''), 1, $value, ['class' => 'checkbox-inline', 'id' => isset($id) ? $id: '', 'disabled' => 'disabled']) !!}<span>@lang($labelKey)</span></label>
@else
<label class="control-label">{!! Form::checkbox($name, 1, $value, ['class' => 'checkbox-inline', 'id' => isset($id) ? $id: '']) !!}<span>@lang($labelKey)</span></label>
@endif

