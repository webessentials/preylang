<div class="row">
	<div class="col-xl-12">
		{!! Form::open(['route' => ['dashboard'], 'method' => 'GET', 'name' => 'searchForm', 'id' => 'form-graph-filter']) !!}
			@include('partials.forms.fieldGroup', ['type' => 'select', 'name' => 'graphType', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.graph_type', 'list' => config('settings.graphs'), 'value' => ''])
			@include('partials.forms.fieldGroup', ['type' => 'multiselect', 'name' => 'provinces[]', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.province', 'list' => array_merge([null => Lang::get('preylang.impact.filter.all')], $provinces), 'value' => ''])
			@include('partials.forms.fieldGroup', ['type' => 'select', 'name' => 'category', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.category', 'list' => [null => Lang::get('preylang.impact.filter.all')] + $categories, 'value' => ''])
			@include('partials.forms.fieldGroup', ['type' => 'select', 'name' => 'sub_category_1', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.subCategory1', 'list' => [null => Lang::get('preylang.impact.filter.all')], 'value' => ''])
			@include('partials.forms.fieldGroup', ['type' => 'datepicker', 'name' => 'dateRange[from]', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.incidentFromDate', 'value' => $firstIncidentDate])
			@include('partials.forms.fieldGroup', ['type' => 'datepicker', 'name' => 'dateRange[to]', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.incidentToDate', 'value' => ''])
			@include('partials.forms.fieldGroup', ['type' => 'select', 'name' => 'edited', 'readOnly' => '', 'labelKey' => 'preylang.impact.filter.edited', 'list' => [null => Lang::get('preylang.impact.filter.all'), 'edited' => Lang::get('preylang.impact.filter.isEdited'), 'not_edited' => Lang::get('preylang.impact.filter.notEdited')], 'value' => ''])
			<div class="pull-right">
				<button class="btn btn-primary" type="submit" id="submit-graph-option">
					@lang('preylang.label.submit')
				</button>
			</div>
		{!! Form::close() !!}
	</div>
</div>
