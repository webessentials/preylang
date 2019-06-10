@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.breadcrumb.category') }}
@endsection

@section('title')
    {{ Lang::get('preylang.breadcrumb.category') }}
@endsection

@section('content')
    @include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'category'])
    <div class="card card-block sameheight-item">
        <div class="title-block">
            <h3 class="title">
                {{ Lang::get('preylang.setting.view.category') }}
            </h3>
        </div>

        @foreach($category->getParents() as $key => $parent)
            @if( !$loop->first && $category->getParents()[(int)$key-1]['level'] !== (int)$parent->level - 1)
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label class="control-label" >{{ Lang::get('preylang.activity.field.subCat  egory' . ((int)$parent->level - 1 )) }}</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control boxed" value="" readonly>
                    </div>
                </div>
            @endif
            <div class="form-group row">
                <div class="col-sm-3">
                    <label class="control-label" >{{ $parent->level == 0 ? Lang::get('preylang.breadcrumb.category') : Lang::get('preylang.activity.field.subCategory' . $parent->level) }}</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control boxed" value="{{ @categoryName([$parent, 'fallback']) }}" readonly>
                </div>
            </div>
            @if( $loop->last && $parent->level !== (int)$category->level - 1)
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label class="control-label" >{{ Lang::get('preylang.activity.field.subCategory' . ((int)$parent->level + 1)) }}</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control boxed" value="" readonly>
                    </div>
                </div>
            @endif
        @endforeach

        <div class="form-group row">
            <div class="col-sm-3">
                <label class="control-label" >{{ $category->level == 0 ? Lang::get('preylang.breadcrumb.category') : Lang::get('preylang.activity.field.subCategory' . $category->level) }}</label>
            </div>
            <div class="col-sm-9">
                <input type="text" class="form-control boxed" value="{{ @categoryName([$category, 'fallback']) }}" readonly>
            </div>
        </div>

        @for($i = $category->level + 1; $i <= 5 ; $i++)
            <div class="form-group row">
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.activity.field.subCategory' . $i) }}</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control boxed" value="" readonly>
                </div>
            </div>
        @endfor

        <div class="form-group row">
            <div class="col-sm-3"></div>
            <div class="col-sm-9">
                <a class="btn btn-secondary" href="{{ route('category.index') }}">
                    <i class="ace-icon fa fa-caret-left bigger-130" aria-hidden="true"></i>
                    {{ Lang::get('preylang.label.back') }}
                </a>
            </div>
        </div>
    </div>
@endsection
