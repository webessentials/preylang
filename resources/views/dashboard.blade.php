@extends('layouts.app')

@section('specificJs')
    <script src="https://www.gstatic.com/charts/loader.js"></script>
@endsection

@section('pageTitle')
{{ Lang::get('preylang.dashboard') }}
@endsection

@section('title')
{{ Lang::get('preylang.dashboard') }}
@endsection

@section('content')
    @include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'home'])
    @can('interact', \App\Models\Impact::class)
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-block">
                        <div class="row">
                            <div class="col-sm-2">
                                @include('partials.dashboard.filters')
                            </div>
                            <div class="col-sm-10">
                                <div class="col-sm-2 col-graph-view-by">
                                    <div class="form-group wrap-graph-view-by pull-right" id="wrap-graph-view-by">
                                        <label for="view-by">@lang('preylang.label.viewBy')</label>
                                        <select id="view-by" class="form-control boxed">
                                            <option value="province">@lang('preylang.impact.filter.province')</option>
                                            <option value="category">@lang('preylang.impact.filter.category')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="position-relative graph-activity-per-month" id="graph-container">
                                    <p class="no-data subtitle" id="nothing-to-show" hidden>@lang('preylang.dashboard.noData')</p>
                                    <div id="graph-activity-per-month" style="width: 100%; height: 500px;"></div> <!--/* Inline style required. */-->
                                    <div class="graph-loading">
                                        <div class="graph-loading-container">
                                            <i class="fa fa-spinner fa-spin fa-4x fa-fw"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right margin-top-10">
                                    <button class="btn btn-primary" id="export-graph">@lang('preylang.dashboard.graph.export')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection
