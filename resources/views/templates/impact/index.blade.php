@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.breadcrumb.impact') }}
@endsection

@section('title')
    {{ Lang::get('preylang.breadcrumb.impact') }}
@endsection

@section('content')
@include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'impact'])
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-block">@include('partials.impact.filters')</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-block">
                @can('process', \App\Models\Impact::class)
                <div class="impact-actions">
                    @include('partials.impact.process')
                    @include('partials.impact.export')
                </div>
                @else
                <div class="impact-actions-left">
                    @include('partials.impact.export')
                </div>
                @endcan

                <div class="table-responsive">
                    @if(Session::has('success'))
                        <div class="alert alert-success">{{ Session::get('success') }}</div>
                    @endif
                    @if(Session::has('error'))
                        <div class="alert alert-danger">{{ Session::get('error') }}</div>
                    @endif
                    <table class="table table-hover table-bordered table-striped impact-list">
                        <thead>
                        <tr>
                            @can('process', \App\Models\Impact::class)
                            <th>
                                <label class="item-check" id="select-all-items">
                                    <input type="checkbox" id="chk-all-item">
                                </label>
                            </th>
                            @endcan
                            <th>@sortablelink('raw_impact_id', trans('preylang.impact.field.no'))</th>
                            <th>@sortablelink('category.raw', trans('preylang.impact.field.category'))</th>
                            <th>@sortablelink('sub_category_1.raw', trans('preylang.impact.field.subCategory1'))</th>
                            <th>@sortablelink('sub_category_2.raw', trans('preylang.impact.field.subCategory2'))</th>
                            <th>@sortablelink('created_at', trans('preylang.impact.field.createdAt'))</th>
                            <th>@sortablelink('report_date', trans('preylang.impact.field.incidentDate'))</th>
                            <th>@sortablelink('villager_id.raw', trans('preylang.villager.list.villagerId'))</th>
                            <th>@lang('preylang.impact.media')</th>
                            <th>@sortablelink('excluded', trans('preylang.impact.field.exclude'))</th>
                            <th>@sortablelink('modified', trans('preylang.impact.field.edited'))</th>
                            <th>@sortablelink('has_location', trans('preylang.impact.field.location'))</th>
                            <th>@lang('preylang.userGroup')</th>
                            <th>
                                @lang('preylang.label.action')
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($impacts as $impact)
                            @include('partials.modal', ['show'=>$impact, 'id'=>'deleteImpact', 'path'=>'impact.delete'])
                            @include('partials.impact.confirmRestore', ['show'=>$impact, 'id'=>'restoreImpact', 'path'=>'impact.restore'])
                            <tr class="{{ $impact->category_modified ? 'edited-row' : ''}}">
                                @can('process', \App\Models\Impact::class)
                                    <td>
                                        <label class="item-check">
                                            <input type="checkbox" class="chk-impact" data="{{ $impact->id }}">
                                        </label>
                                    </td>
                                @endcan
                                <td>{{ $impact->impact_number }}</td>
                                <td>
                                    {{ @categoryName([$impact->category]) }}
                                </td>
                                <td>
                                    {{ @categoryName([$impact->getCategoryByRelationField('sub_category_1'), $impact->categories['sub_category_1']]) }}
                                </td>
                                <td>
                                    {{ @categoryName([$impact->getCategoryByRelationField('sub_category_2'), $impact->categories['sub_category_2']]) }}
                                </td>
                                <td>{{ $impact->created_at }}</td>
                                <td>{{ $impact->report_date }}</td>
                                <td>{{ $impact->villager->name }}</td>
                                <td>
                                    <div class="list-inline-item">
                                        @if($impact->image)
                                        <div class="image-gallery">
                                            @include('partials.impact.imageFile', ['identifier' => 'image-' . $impact->id, 'files' => $impact->images])
                                        </div>
                                        @endif
                                        @if($impact->facebook)<i class="ace-icon fa fa-facebook-square"></i>@endif

                                        @if($impact->audio)
                                        <div class="image-gallery">
                                            @include('partials.impact.audioFile', ['identifier' => 'audio-' . $impact->id, 'files' => $impact->audios])
                                        </div>
                                        @endif
                                    </div>

                                </td>
                                <td class="impact-table-cell-center">@if($impact->excluded) <i class="ace-icon fa fa-check icon-status-large"></i>@else <i class="ace-icon fa fa-times icon-status-large"></i>@endif</td>
                                <td class="impact-table-cell-center">@if($impact->modified) <i class="ace-icon fa fa-check icon-status-large"></i>@else <i class="ace-icon fa fa-times icon-status-large"></i>@endif</td>
                                <td class="impact-table-cell-center">
                                    @if($impact->latitude)
                                        @include('partials.impact.displayMap', ['identifier' => 'map-' . $impact->id,'latitude' =>  $impact->latitude, 'longitude' => $impact->longitude])
                                    @endif
                                </td>
                                <td>@if($impact->villager->userGroup) {{ $impact->villager->userGroup->name }} @else {{ Lang::get('preylang.application.site.title') }} @endif</td>
                                <td>
                                    <a class="btn green" title="show" href="{{ route('impact.show', $impact) }}">
                                        <i class="ace-icon fa fa-eye bigger-130" aria-hidden="true"></i>
                                    </a>
                                    @can('interactWithButtons', $impact)
                                        <a class="btn green" title="{{ Lang::get('preylang.edit') }}" href="{{ route('impact.edit', $impact) }}">
                                            <i class="ace-icon fa fa-pencil bigger-130" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" class="btn green" title="{{ Lang::get('preylang.delete') }}" data-toggle="modal" data-target="#deleteImpactModal-{{ $impact->id }}">
                                            <i class="ace-icon fa fa-trash-o bigger-130" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" class="btn green" title="{{ Lang::get('preylang.restore') }}" data-toggle="modal" data-target="#restoreImpactModal-{{ $impact->id }}">
                                            <i class="ace-icon fa fa-undo bigger-130" aria-hidden="true"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('partials.totalRecord', ['start' => $impacts->firstItem(), 'end' => $impacts->lastItem(), 'total' => $impacts->total()])
{!! $impacts->appends(\Request::except('page', '_token', 'query'))->render() !!}
@if(Session::has('download.next.request'))
    <script type="text/javascript">
        window.location.href = '{{ route("files.download", Session::get("download.next.request"))}}'
    </script>
@endif
@endsection

