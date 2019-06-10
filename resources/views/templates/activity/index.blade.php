@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.breadcrumb.activity') }}
@endsection

@section('title')
    {{ Lang::get('preylang.breadcrumb.activity') }}
@endsection

@section('content')
@include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'activity'])
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-block">
                <div class="row">
                    <div class="col-md-6">
                        @include("partials.recordsPerPage", ['recordsPerPage' => $recordsPerPage])
                    </div>
                    <div class="col-md-6">
                        <div class="pull-right">
                            @include("partials.searchBox", ['keyword' => $keyword])
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped tbl-activity-list">
                        <thead>
                        <tr>
                            <th class="fixed-col">@sortablelink('raw_impact_id', trans('preylang.activity.impactNumber'))</th>
                            <th class="fixed-col">@sortablelink('modified_date', trans('preylang.activity.modifyDate'))</th>
                            <th>@sortablelink('category_path.raw', trans('preylang.impact.filter.category'))</th>
                            <th>@sortablelink('user_email.raw', trans('preylang.user'))</th>
                            <th>@sortablelink('field_list.raw', trans('preylang.activity.fieldList'))</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($activities as $activity)
                            <tr>
                                <td>
                                    @can('interactWithButtons', App\Models\Impact::class)
                                        <a href="{{ route('impact.edit', $activity->impact_id) }}" target="_blank">
                                    @endcan
                                    @cannot('interactWithButtons', App\Models\Impact::class)
                                        <a href="{{ route('impact.show', $activity->impact_id) }}" target="_blank">
                                    @endcan
                                    {{ $activity->impact ? $activity->impact->impact_number : '' }}
                                    </a>
                                </td>
                                <td>{{ $activity->updated_at }}</td>
                                @if ($activity->impact)
                                    <td>{{ $activity->impact->getCategoryPath() }}</td>
                                @else
                                    <td></td>
                                @endif
                                <td>{{ $activity->user->email }}</td>
                                <td>{!! ActivityHelper::renderFieldList($activity->field_list) !!}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('partials.totalRecord', ['start' => $activities->firstItem(), 'end' => $activities->lastItem(), 'total' => $activities->total()])
{!! $activities->appends(\Request::except('page'))->render() !!}
@endsection
