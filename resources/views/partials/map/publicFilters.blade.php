{!! Form::open(['route' => ['map'], 'method' => 'GET', 'name' => 'searchForm', 'id' => 'form-map-filter']) !!}
<div class="row">
    <div class="col-md-4 col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'select', 'name' => 'sub_category_1', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.category', 'list' => [null => Lang::get('preylang.impact.filter.all')] + $categories, 'value' => $subCategory1])
    </div>
    <div class="col-md-4 col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'datepicker', 'name' => 'dateRange[from]', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.incidentFromDate', 'value' => $dateRangeFrom])
    </div>
    <div class="col-md-4 col-xl-4">
        @include('partials.forms.fieldGroup', ['type' => 'datepicker', 'name' => 'dateRange[to]', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.incidentToDate', 'value' => $dateRangeTo])
    </div>
</div>
<div class="row">
    <div class="col-xl-12 pull-right">
        <div class="public-map-action pull-right">
            <button class="btn btn-primary" type="submit">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none"></span>
                @lang('preylang.impact.filter.search')
            </button>
            <a class="btn btn-primary btn-reset-filter" href="{{ route('public.map') }}">
                @lang('preylang.impact.filter.reset')
            </a>
        </div>
    </div>
</div>
{!! Form::close() !!}
