<div class="row">
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => 'readonly', 'labelKey' => 'preylang.rawImpact.field.no', 'value' => $rawImpact->id])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', [
        'type' => 'text',
        'readOnly' => 'readonly',
        'labelKey' => 'preylang.villager.list.deviceId',
        'addition' => '1',
        'villagerShowRoute' => route('villager.show', $rawImpact->villager->id),
        'villagerId' => $rawImpact->villager->name,
        'value' => $rawImpact->villager->device_imei,
        'name' => 'device_imei'])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.reportedDate', 'value' => $rawImpact->report_date])
    </div>
</div>
<div class="row">
    <div class="col-xl-4">
        @if($rawImpact->categories['category'])
           @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.category', 'value' => CategoryHelper::getCategoryName([$rawImpact->getCategoryByRelationField('category'), $rawImpact->categories['category']]) ])
        @else
           @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.category', 'value' => ''])
        @endif
    </div>
    <div class="col-xl-4">
        @if($rawImpact->categories['sub_category_1'])
           @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.subCategory1', 'value' => CategoryHelper::getCategoryName([$rawImpact->getCategoryByRelationField('sub_category_1'), $rawImpact->categories['sub_category_1']]) ])
        @else
           @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.subCategory1', 'value' => ''])
        @endif
    </div>
    <div class="col-xl-4">
        @if($rawImpact->categories['sub_category_2'])
            @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.subCategory2', 'value' => CategoryHelper::getCategoryName([$rawImpact->getCategoryByRelationField('sub_category_2'), $rawImpact->categories['sub_category_2']]) ])
        @else
            @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.subCategory2', 'value' => ''])
        @endif
    </div>
</div>
<div class="row">
    <div class="col-xl-4">
        @if($rawImpact->categories['sub_category_3'])
            @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.subCategory3', 'value' => CategoryHelper::getCategoryName([$rawImpact->getCategoryByRelationField('sub_category_3'), $rawImpact->categories['sub_category_3']]) ])
        @else
            @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.subCategory3', 'value' => ''])
        @endif
    </div>
    <div class="col-xl-4">
        @if($rawImpact->categories['sub_category_4'])
            @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.subCategory4', 'value' => CategoryHelper::getCategoryName([$rawImpact->getCategoryByRelationField('sub_category_4'), $rawImpact->categories['sub_category_4']]) ])
        @else
            @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.subCategory4', 'value' => ''])
        @endif
    </div>
    <div class="col-xl-4">
        @if($rawImpact->categories['sub_category_5'])
            @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.subCategory5', 'value' => CategoryHelper::getCategoryName([$rawImpact->getCategoryByRelationField('sub_category_5'), $rawImpact->categories['sub_category_5']]) ])
        @else
            @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.subCategory5', 'value' => ''])
        @endif
    </div>
</div>
<div class="row">
    <div class="col-xl-4">
        @if($rawImpact->categories['permit'])
            @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.permit', 'value' => $rawImpact->categories['permit']])
        @else
            @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.permit', 'value' => ''])
        @endif
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => 'readonly', 'labelKey' => 'preylang.rawImpact.field.longitude', 'value' => $rawImpact->longitude])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => 'readonly', 'labelKey' => 'preylang.rawImpact.field.latitude', 'value' => $rawImpact->latitude])
    </div>
</div>
<div class="row">
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'textarea', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.note', 'value' => $rawImpact->note])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'textarea', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.patrollerNote', 'value' => $rawImpact->patroller_note])
    </div>
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.numberOfItem', 'value' => $rawImpact->number_of_items])
    </div>
</div>
<div class="row">
    <div class="col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'text', 'readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.reportTo', 'value' => $rawImpact->report_to])
    </div>
</div>
<div class="row">
    <div class="col-xl-8">
        <div class="form-group">
            <div>
                @include('partials.forms.inlineCheckbox', ['readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.byVisual', 'value' => $rawImpact->by_visual])
                @include('partials.forms.inlineCheckbox', ['readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.byAudio', 'value' => $rawImpact->by_audio])
                @include('partials.forms.inlineCheckbox', ['readOnly' => $readOnly, 'labelKey' => 'preylang.rawImpact.field.byTrack', 'value' => $rawImpact->by_track])
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 text-center">
        <a class="btn btn-secondary" href="{{ route('rawImpact.index') }}">
            <i class="ace-icon fa fa-caret-left" aria-hidden="true"></i>
            {{ Lang::get('preylang.label.back') }}
        </a>
    </div>
</div>
