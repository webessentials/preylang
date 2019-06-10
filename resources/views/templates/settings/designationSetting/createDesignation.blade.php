@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.setting.designation') }}
@endsection

@section('title')
    {{ Lang::get('preylang.setting.designation') }}
@endsection

@section('content')
    @include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'designation'])
    <div class="card card-block sameheight-item">
        <div class="title-block">
            <h3 class="title">
                {{ Lang::get('preylang.setting.createNew', ['setting' => Lang::get('preylang.setting.designation')]) }}
            </h3>
        </div>

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

        <form role="form" method="POST" action="{{ route('designation.store') }}">
            @csrf
            <input name="type" value="designation" type="hidden">
            <div class="form-group row {{ $errors->has('name') ? 'has-error' : '' }}">
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.englishName') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control boxed {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name" value="{{ old('name') }}" autofocus>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                    <a class="btn btn-secondary" href="{{ route('designation.index') }}">
                        <i class="ace-icon fa fa-ban bigger-130" aria-hidden="true"></i>
                        {{ Lang::get('preylang.abort') }}
                    </a>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> {{ Lang::get('preylang.save') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
