@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.breadcrumb.category') }}
@endsection

@section('title')
    {{ Lang::get('preylang.breadcrumb.category') }}
@endsection

@section('content')
    @include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'category'])
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-block">
                    @if(Session::has('error'))
                        <div class="alert alert-danger">{{ Session::get('error') }}</div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            @include("partials.recordsPerPage", ['recordsPerPage' => $recordsPerPage])
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>@sortablelink('level0', trans('preylang.breadcrumb.category'))</th>
                                <th>@sortablelink('level1', trans('preylang.category.list.subCat1'))</th>
                                <th>@sortablelink('level2', trans('preylang.category.list.subCat2'))</th>
                                <th>@sortablelink('level3', trans('preylang.category.list.subCat3'))</th>
                                <th>@sortablelink('level4', trans('preylang.category.list.subCat4'))</th>
                                <th>@sortablelink('level5', trans('preylang.category.list.leaf'))</th>
                                <th>{{ Lang::get('preylang.label.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @isset($categories)
                                @foreach($categories as $category)
                                    <tr>
                                        <td>
                                            {{ $category->name }}
                                        </td>
                                        <td>
                                            {{ $category->child1 }}
                                        </td>
                                        <td>
                                            {{ $category->child2 }}
                                        </td>
                                        <td>
                                            {{ $category->child3 }}
                                        </td>
                                        <td>
                                            {{ $category->child4 }}
                                        </td>
                                        <td>
                                            {{ $category->child5 }}
                                        </td>
                                        <td>
                                            @php
                                                if (isset($category->id5)) {
                                                    $id = $category->id5;
                                                } elseif (isset($category->id4)) {
                                                    $id = $category->id4;
                                                } elseif (isset($category->id3)) {
                                                    $id = $category->id3;
                                                } elseif (isset($category->id2)) {
                                                    $id = $category->id2;
                                                } elseif (isset($category->id1)) {
                                                    $id = $category->id1;
                                                } else {
                                                    $id = $category->id;
                                                }
                                            @endphp
                                            <a class="btn green" title="{{ Lang::get('preylang.label.view') }}"
                                               href="{{ route('category.show', $id) }}">
                                                <i class="ace-icon fa fa-eye bigger-130" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endisset
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.totalRecord', ['start' => $categories->firstItem(), 'end' => $categories->lastItem(), 'total' => $categories->total()])
    {!! $categories->appends(\Request::except('page'))->render() !!}
@endsection
