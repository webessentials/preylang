{!! Form::open(['route' => [$isFilterSubMenu == true ? 'impact.filterByCategory' : 'impact.filter', $searchFields['category']], 'method' => 'GET', 'name' => 'searchForm', 'id' => 'form-impact-filter']) !!}
<div class="row">
    <div class="col-xl-3">
        @include('partials.forms.fieldGroup', ['type' => 'multiselect', 'name' => 'provinces[]', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.province', 'list' => array_merge([null => Lang::get('preylang.impact.filter.all')], $provinces), 'value' => $searchFields['provinces']])
    </div>
    <div class="col-xl-3 {{ $isFilterSubMenu == true ? 'hidden': '' }}">
        @include('partials.forms.fieldGroup', ['type' => 'select', 'name' => 'category', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.category', 'list' => [null => Lang::get('preylang.impact.filter.all')] + $categories, 'value' => $searchFields['category']])
    </div>
    <div class="{{ $isFilterSubMenu == true ? 'col-xl-3': 'col-xl-2' }}">
        @include('partials.forms.fieldGroup', ['type' => 'select', 'name' => 'sub_category_1', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.subCategory1', 'list' => [null => Lang::get('preylang.impact.filter.all')] + $subCategories1, 'value' => $searchFields['sub_category_1']])
    </div>
    <div class="col-xl-2">
        @include('partials.forms.fieldGroup', ['type' => 'datepicker', 'name' => 'dateRange[from]', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.incidentFromDate', 'value' => $searchFields['dateRange']['from']])
    </div>
    <div class="col-xl-2">
        @include('partials.forms.fieldGroup', ['type' => 'datepicker', 'name' => 'dateRange[to]', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.incidentToDate', 'value' => $searchFields['dateRange']['to']])
    </div>
</div>
<div class="row">
    <div class="col-xl-3">
        @include('partials.forms.fieldGroup', ['type' => 'text', 'name' => 'keyword', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.search', 'value' => $searchFields['keyword']])
    </div>
    <div class="col-xl-3">
        @include('partials.forms.fieldGroup', ['type' => 'select', 'name' => 'edited', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.edited', 'list' => [null => Lang::get('preylang.impact.filter.all'), 'edited' => Lang::get('preylang.impact.filter.isEdited'), 'not_edited' => Lang::get('preylang.impact.filter.notEdited'), 'category_modified' => Lang::get('preylang.impact.filter.modified')], 'value' => $searchFields['edited']])
    </div>
    <div class="col-xl-2">
        <div class="form-group">
            <label>&nbsp;</label><br>
            @include('partials.forms.inlineCheckbox', ['name' => 'image', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.withImage', 'value' => $searchFields['image']])
        </div>
    </div>
    <div class="col-xl-2">
        <div class="form-group">
            <label>&nbsp;</label><br>
            @include('partials.forms.inlineCheckbox', ['name' => 'audio', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.withAudio', 'value' => $searchFields['audio']])
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12 pull-right">
        <div class="pull-right">
            <input class="btn btn-primary" type="submit" name="" value="@lang('preylang.impact.filter.search')">
            <a class="btn btn-primary btn-reset-filter" href="{{ $isFilterSubMenu == true ? route('impact.filterByCategory', $searchFields['category']) : route('impact.index') }}">
                @lang('preylang.impact.filter.reset')
            </a>
        </div>
    </div>
</div>
{!! Form::close() !!}
