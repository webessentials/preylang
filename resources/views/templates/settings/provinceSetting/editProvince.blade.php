@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.province') }}
@endsection

@section('title')
    {{ Lang::get('preylang.province') }}
@endsection

@section('content')
    @include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'province'])
    <div class="card card-block sameheight-item">
        <div class="title-block">
            <h3 class="title">
                {{ Lang::get('preylang.setting.edit', ['setting' => Lang::get('preylang.province')]) }}
            </h3>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <ul>
                        <li>{{ $error }}</li>
                    </ul>
                @endforeach
            </div>
        @endif

        <form role="form" method="POST" action="{{ route('province.update', $province->id) }}">
            @csrf
            @method('PUT')
            <input name="type" value="province" type="hidden">
            <div class="form-group row {{ $errors->has('name') ? 'has-error' : '' }}">
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.englishName') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control boxed {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name" value="{{ $errors->has('name') ? old('name') : $province->name }}" autofocus>
                </div>
            </div>

            <div class="form-group row {{ $errors->has('name_kh') ? 'has-error' : '' }}">
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.khmerName') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control boxed {{ $errors->has('name_kh') ? 'is-invalid' : '' }}" id="name_kh" name="name_kh" value="{{ $errors->has('name_kh') ? old('name_kh') : $province->name_kh }}" autofocus>
                </div>
            </div>

            <div class="form-group row {{ $errors->has('sys_value') ? 'has-error' : '' }}">
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.setting.form.province.unique.id') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control boxed" value="{{ $province->sys_value }}" autofocus required disabled>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                    <a class="btn btn-secondary" href="{{ route('province.index') }}">
                        <i class="ace-icon fa fa-ban bigger-130" aria-hidden="true"></i>
                        {{ Lang::get('preylang.abort') }}
                    </a>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> {{ Lang::get('preylang.save') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
