@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.breadcrumb.rawImpact') }}
@endsection

@section('title')
    {{ Lang::get('preylang.breadcrumb.rawImpact') }}
@endsection

@section('content')
@include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'rawImpact'])
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>@sortablelink('id', trans('preylang.rawImpact.field.no'))</th>
                            <th>@sortablelink('category.raw', trans('preylang.rawImpact.field.category'))</th>
                            <th>@sortablelink('sub_category_1.raw', trans('preylang.rawImpact.field.subCategory1'))</th>
                            <th>@sortablelink('sub_category_2.raw', trans('preylang.rawImpact.field.subCategory2'))</th>
                            <th>@sortablelink('sub_category_3.raw', trans('preylang.rawImpact.field.subCategory3'))</th>
                            <th>@sortablelink('sub_category_4.raw', trans('preylang.rawImpact.field.subCategory4'))</th>
                            <th>@sortablelink('leaf_category.raw', trans('preylang.rawImpact.field.subCategory5'))</th>
                            <th>@lang('preylang.userGroup')</th>
                            <th>@sortablelink('number_of_items', trans('preylang.rawImpact.field.numberOfItem'))</th>
                            <th>@sortablelink('report_date', trans('preylang.rawImpact.field.reportedDate'))</th>
                            <th>@sortablelink('villager_id.raw', trans('preylang.rawImpact.field.villagerId'))</th>
                            <th>@lang('preylang.rawImpact.field.action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($rawImpacts as $rawImpact)
                            <tr>
                                <td>{{ $rawImpact->id }}</td>
                                <td>
                                    {{ @categoryName([$rawImpact->getCategoryByRelationField('category'), $rawImpact->categories['category']]) }}
                                </td>
                                <td>
                                    {{ @categoryName([$rawImpact->getCategoryByRelationField('sub_category_1'), $rawImpact->categories['sub_category_1']]) }}
                                </td>
                                <td>
                                    {{ @categoryName([$rawImpact->getCategoryByRelationField('sub_category_2'), $rawImpact->categories['sub_category_2']]) }}
                                </td>
                                <td>
                                    {{ @categoryName([$rawImpact->getCategoryByRelationField('sub_category_3'), $rawImpact->categories['sub_category_3']]) }}
                                </td>
                                <td>
                                    {{ @categoryName([$rawImpact->getCategoryByRelationField('sub_category_4'), $rawImpact->categories['sub_category_4']]) }}
                                </td>
                                <td>
                                    {{ @categoryName([$rawImpact->getCategoryByRelationField('sub_category_5'), $rawImpact->categories['sub_category_5']]) }}
                                </td>
                                <td>@if($rawImpact->villager->userGroup) {{ $rawImpact->villager->userGroup->name }} @else {{ Lang::get('preylang.application.site.title') }} @endif</td>
                                <td>{{ $rawImpact->number_of_items }}</td>
                                <td>{{ $rawImpact->report_date }}</td>
                                <td>{{ $rawImpact->villager->name }}</td>
                                <td>
                                    <a class="btn green" title="show" href="{{ route('rawImpact.show', $rawImpact) }}">
                                        <i class="ace-icon fa fa-eye bigger-130" aria-hidden="true"></i>
                                    </a>
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
@include('partials.totalRecord', ['start' => $rawImpacts->firstItem(), 'end' => $rawImpacts->lastItem(), 'total' => $rawImpacts->total()])
{!! $rawImpacts->appends(\Request::except('page'))->render() !!}
@endsection
