{!! Form::open(['route' => ['map'], 'method' => 'GET', 'name' => 'searchForm', 'id' => 'form-map-filter']) !!}
<div class="row">
	<div class="col-md-6 col-xl-3">
		@include('partials.forms.fieldGroup', ['type' => 'select', 'name' => 'category', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.category', 'list' => [null => Lang::get('preylang.impact.filter.all')] + $categories, 'value' => ''])
	</div>
	<div class="col-md-6 col-xl-3">
		@include('partials.forms.fieldGroup', ['type' => 'select', 'name' => 'sub_category_1', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.subCategory1', 'list' => [null => Lang::get('preylang.impact.filter.all')], 'value' => ''])
	</div>
	<div class="col-md-6 col-xl-2">
		@include('partials.forms.fieldGroup', ['type' => 'datepicker', 'name' => 'dateRange[from]', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.incidentFromDate', 'value' => ''])
	</div>
	<div class="col-md-6 col-xl-2">
		@include('partials.forms.fieldGroup', ['type' => 'datepicker', 'name' => 'dateRange[to]', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.incidentToDate', 'value' => ''])
	</div>
	<div class="col-md-6 col-xl-2">
		@include('partials.forms.fieldGroup', ['type' => 'select', 'name' => 'edited', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.edited', 'list' => [null => Lang::get('preylang.impact.filter.all'), 'edited' => Lang::get('preylang.impact.filter.isEdited'), 'not_edited' => Lang::get('preylang.impact.filter.notEdited'), 'category_modified' => Lang::get('preylang.impact.filter.modified')], 'value' => ''])
	</div>
</div>
<div class="row">
	<div class="col-xl-12 pull-right">
		<div class="pull-right">
			<button class="btn btn-primary" type="submit">
				<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none"></span>
				@lang('preylang.impact.filter.search')
			</button>
			<a class="btn btn-primary btn-reset-filter" href="{{ route('map') }}">
				@lang('preylang.impact.filter.reset')
			</a>
		</div>
	</div>
</div>
{!! Form::close() !!}
