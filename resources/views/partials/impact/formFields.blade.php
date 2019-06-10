@php
$name = '';
@endphp

<div>
    {!! Form::hidden('reporting-categories', $reportingCategories) !!}
    {!! Form::hidden('logging-categories', $loggingCategories) !!}
    {!! Form::hidden('dontknow-categories', $dontKnowCategories) !!}
    {!! Form::hidden('interactionno-categories', $interactionNoCategories) !!}
    {!! Form::hidden('other-categories', $otherCategories) !!}
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="alert alert-success">
            {{ $readOnly ? Lang::get('preylang.impact.header.detail') . ' - ' : Lang::get('preylang.impact.edit') . ' - '}}
            {{ $impact->getCategoryPath() }}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => 'readonly', 'labelKey' => 'preylang.impact.field.no', 'value' => $impact->impact_number, 'name' => 'no'])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', [
        'type' => 'text',
        'readOnly' => 'readonly',
        'labelKey' => 'preylang.villager.list.deviceId',
        'addition' => '1',
        'villagerShowRoute' => route('villager.show', $impact->villager->id),
        'villagerId' => $impact->villager->name,
        'value' => $impact->villager->device_imei,
        'name' => 'device_imei'])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'datetimepicker', 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.reportedDate', 'value' => $impact->report_date, 'name' => 'report_date'])
    </div>
</div>
<div class="row">
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'datetimepicker', 'readOnly' => 'readonly', 'labelKey' => 'preylang.impact.field.createdAt', 'value' => $impact->created_at, 'name' => 'created_at'])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'select', 'list' => $categories[0], 'readOnly' => $readOnly, 'name' => 'category', 'labelKey' => 'preylang.impact.field.category', 'value' => is_object($impact->category) ? $impact->category->id : ''])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'select', 'list' => $categories[1], 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.subCategory1', 'value' => is_object($impact->subCategory1) ? $impact->subCategory1->id : '', 'name' => 'sub_category_1', 'canAdd' => true, 'canModify' => is_object($impact->category) ? $impact->category->modify_child : false])
    </div>
</div>
<div class="row">
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'select', 'list' => $categories[2], 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.subCategory2', 'value' => is_object($impact->subCategory2) ? $impact->subCategory2->id : '', 'name' => 'sub_category_2', 'canAdd' => true, 'canModify' => is_object($impact->subCategory1) ? $impact->subCategory1->modify_child : false])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'select', 'list' => $categories[3], 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.subCategory3', 'value' => is_object($impact->subCategory3) ? $impact->subCategory3->id : '', 'name' => 'sub_category_3', 'canAdd' => true, 'canModify' => is_object($impact->subCategory2) ? $impact->subCategory2->modify_child : false])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'select', 'list' => $categories[4], 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.subCategory4', 'value' => is_object($impact->subCategory4) ? $impact->subCategory4->id : '', 'name' => 'sub_category_4', 'canAdd' => true, 'canModify' => is_object($impact->subCategory3) ? $impact->subCategory3->modify_child : false])
    </div>
</div>
<div class="row">
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'select', 'list' => $categories[5], 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.subCategory5', 'value' => is_object($impact->subCategory5) ? $impact->subCategory5->id : '', 'name' => 'sub_category_5', 'canAdd' => true, 'canModify' => is_object($impact->subCategory4) ? $impact->subCategory4->modify_child : false])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'select', 'list' => [null => ''] + Config::get('settings.permits'), 'labelKey' => 'preylang.impact.field.permit', 'value' => $impact->categories['permit'], 'name' => 'permit'])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => 'readonly', 'labelKey' => 'preylang.impact.field.longitude', 'value' => $impact->longitude, 'name' => 'longitude'])
    </div>
</div>
<div class="row">
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => 'readonly', 'labelKey' => 'preylang.impact.field.latitude', 'value' => $impact->latitude, 'name' => 'latitude'])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'textarea', 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.note', 'value' => $impact->note, 'name' => 'note'])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'textarea', 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.noteKh', 'value' => $impact->note_kh, 'name' => 'note_kh'])
    </div>
</div>
<div class="row">
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'textarea', 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.patrollerNote', 'value' => $impact->patroller_note, 'name' => 'patroller_note'])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'number', 'min' => '0', 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.numberOfItem', 'value' => $impact->number_of_items, 'name' => 'number_of_items'])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.name', 'value' => $impact->name, 'name' => 'name'])
    </div>
</div>
<div class="row">
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.employer', 'value' => $impact->employer, 'name' => 'employer'])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.license', 'value' => $impact->license, 'name' => 'license'])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.reportTo', 'value' => $impact->report_to, 'name' => 'report_to'])
    </div>
</div>

<div class="form-group row reporting-fields">
    <div class="col-xl-4">
        <label class="control-label">{{ Lang::get('preylang.setting.victimType') }}</label>
        @if($impact->victimType)
        {!! Form::select('victim_type_id', $victimTypes, $impact->victimType->id, ['class' => 'form-control']) !!}
        @else
        {!! Form::select('victim_type_id', $victimTypes, '', ['class' => 'form-control']) !!}
        @endif
    </div>
    <div class="col-xl-4 reporting-fields">
        <label class="control-label">{{ Lang::get('preylang.setting.reason') }}</label>
        @if($impact->reason)
        {!! Form::select('reason_id', $reasons, $impact->reason->id, ['class' => 'form-control']) !!}
        @else
        {!! Form::select('reason_id', $reasons, '', ['class' => 'form-control']) !!}
        @endif
    </div>
    <div class="col-xl-4 reporting-fields">
        <label class="control-label">{{ Lang::get('preylang.setting.offender') }}</label>
        @if($impact->offender)
        {!! Form::select('offender_id', $offenders, $impact->offender->id, ['class' => 'form-control']) !!}
        @else
        {!! Form::select('offender_id', $offenders, '', ['class' => 'form-control']) !!}
        @endif
    </div>
</div>

<div class="form-group row reporting-fields">
    <div class="col-xl-4">
        <label class="control-label">{{ Lang::get('preylang.setting.threatening') }}</label>
        @if($impact->threatening)
        {!! Form::select('threatening_id', $threatenings, $impact->threatening->id, ['class' => 'form-control']) !!}
        @else
        {!! Form::select('threatening_id', $threatenings, '', ['class' => 'form-control']) !!}
        @endif
    </div>
    <div class="col-xl-4 reporting-fields">
        <label class="control-label">{{ Lang::get('preylang.setting.designation') }}</label>
        @if($impact->designation)
        {!! Form::select('designation_id', $designations, $impact->designation->id, ['class' => 'form-control']) !!}
        @else
        {!! Form::select('designation_id', $designations, '', ['class' => 'form-control']) !!}
        @endif
    </div>
    <div class="col-xl-4 reporting-fields">
        <label class="control-label">{{ Lang::get('preylang.setting.proof') }}</label>
        @if($impact->proof)
        {!! Form::select('proof_id', $proofs, $impact->proof->id, ['class' => 'form-control']) !!}
        @else
        {!! Form::select('proof_id', $proofs, '', ['class' => 'form-control']) !!}
        @endif
    </div>
</div>

<div class="form-group row reporting-fields">
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'textarea', 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.witness', 'value' => $impact->witness, 'name' => 'witness'])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'textarea', 'readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.location', 'value' => $impact->location, 'name' => 'location'])
    </div>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="form-group">
            <div>
                @include('partials.forms.inlineCheckbox', ['readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.agreement', 'value' => $impact->agreement, 'name' => 'agreement', 'id' => 'chkAgreement'])
                @include('partials.forms.inlineCheckbox', ['readOnly' => 'readOnly', 'labelKey' => 'preylang.impact.field.categoryModified', 'value' => $impact->category_modified, 'name'=>'category_modified', 'id' => 'category_modified'])
                @include('partials.forms.inlineCheckbox', ['readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.burnedWood', 'value' => $impact->burned_wood, 'name' => 'burned_wood', 'id' => 'chkBurnedWood'])
                @include('partials.forms.inlineCheckbox', ['readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.byVisual', 'value' => $impact->by_visual, 'name' => 'by_visual', 'id' => 'chkByVisual'])
                @include('partials.forms.inlineCheckbox', ['readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.byAudio', 'value' => $impact->by_audio, 'name' => 'by_audio', 'id' => 'chkByAudio'])
                @include('partials.forms.inlineCheckbox', ['readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.byTrack', 'value' => $impact->by_track, 'name' => 'by_track', 'id' => 'chkByTrack'])
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        @include('partials.forms.inlineCheckbox', ['readOnly' => $readOnly, 'labelKey' => 'preylang.impact.field.exclude', 'value' => $impact->excluded, 'name' => 'excluded', 'id' => 'chkExcluded'])
    </div>
    <div class="col-xl-2">
        @if($impact->excluded === 1)
        <div class="form-group" id="wrapExcludedReason">
        @else
        <div class="form-group hidden" id="wrapExcludedReason">
        @endif

            @if($impact->excluded)
                @if($readOnly)
                    @if($impact->excluded_note)
                        <input type="text" class="form-control boxed" value="{{ $impact->excluded_note }}" readonly="readonly">
                    @else
                        @if($impact->excluded_reason_id)
                            {!! Form::select('excluded_reason_id', $excludedReasons, $impact->excluded_reason_id, ['class' => 'form-control']) !!}
                        @else
                            <input type="text" class="form-control boxed" value="" readonly="readonly">
                        @endif
                    @endif
                @else
                    @if($impact->excluded_reason_id)
                        {!! Form::select('excluded_reason_id', $excludedReasons, $impact->excluded_reason_id, ['class' => 'form-control']) !!}
                    @else
                        {!! Form::select('excluded_reason_id', $excludedReasons, '', ['class' => 'form-control']) !!}
                    @endif
                @endif
            @else
                @if(! $readOnly)
                    {!! Form::select('excluded_reason_id', $excludedReasons, null, ['class' => 'form-control']) !!}
                @endif
            @endif
        </div>

    </div>
</div>

<div class="form-group row">
    <div class="col-xl-3">
        @if($impact->audio)
            @foreach($impact->audios as $audio)
                <audio class="file-audio" controls="controls">
                    <source src="{{ route('files.get', $audio->file_name) }}" type="audio/mpeg">
                </audio>
            @endforeach
        @endif
    </div>
    <div class="col-xl-9">
        @if($impact->image)
            <div class="row image-gallery">
                @include('partials.impact.imageFile', ['identifier' => 'image-' . $impact->id, 'files' => $impact->images, 'displayThumbnail' => true])
            </div>
        @endif
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-12 text-center">
        <a class="btn btn-secondary" href="{{ route('impact.index') }}">
            @if(! $readOnly)
                <i class="ace-icon fa fa-ban bigger-130" aria-hidden="true"></i>
                {{ Lang::get('preylang.abort') }}
            @else
                <i class="ace-icon fa fa-caret-left" aria-hidden="true"></i>
                {{ Lang::get('preylang.label.back') }}
            @endif
        </a>

        @if(! $readOnly)
            <button id="save-impact" class="btn btn-primary" type="submit"><i class="fa fa-save"></i> {{ Lang::get('preylang.save') }}</button>
        @endif
    </div>
</div>
