@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.user') }}
@endsection

@section('title')
{{ Lang::get('preylang.userSetting') }}
@endsection

@section('content')
@include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'user.userSetting'])
<div class="card card-block sameheight-item">
    <div class="title-block">
        <h3 class="title">
            {{ Lang::get('preylang.userSetting') }}
        </h3>
    </div>
    @if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    @if(Session::has('error'))
    <div class="alert alert-warning">{{ Session::get('error') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
        <ul>
            <li>{{ $error }}</li>
        </ul>
        @endforeach
    </div>
    @endif
    <form role="form" method="POST" action="{{ route('user.updateUserSetting') }}">
        @csrf
        @method('PUT')
        <div class="form-group row" {{ $errors->has('language') ? 'has-error' : '' }}>
            <div class="col-sm-2">
                <label class="control-label" >{{ Lang::get('preylang.userSetting.language') }}</label>
            </div>
            <div class="col-sm-4">
                <select class="form-control boxed" name="language_key" id="language_key">
                    <option value="en" {{ $user->language_key == 'en' ? 'selected' : '' }}>{{ Lang::get('preylang.userSetting.english') }}</option>
                    <option value="km" {{ $user->language_key == 'km' ? 'selected' : '' }}>{{ Lang::get('preylang.userSetting.khmer') }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <div class="col-sm-10">
                <a class="btn btn-secondary" href="{{ route('dashboard') }}">
                    <i class="ace-icon fa fa-ban bigger-130" aria-hidden="true"></i>
                    {{ Lang::get('preylang.abort') }}
                </a>
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> {{ Lang::get('preylang.save') }}</button>
            </div>
        </div>
    </form>
</div>
@endsection
