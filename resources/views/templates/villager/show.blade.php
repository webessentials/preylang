@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.sidebar.villager') }}
@endsection

@section('title')
    {{ Lang::get('preylang.sidebar.villager') }}
@endsection

@section('content')
    @include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'villager'])
    <div class="card card-block sameheight-item">
        <div class="title-block">
            <h3 class="title">
                {{ Lang::get('preylang.villager.showVillager') }}
            </h3>
        </div>
        <div class="form-group row">
            <div class="col-sm-3">
                <label class="control-label" >{{ Lang::get('preylang.villager.list.villagerId') }}</label>
            </div>
            <div class="col-sm-9">
                <input type="text" class="form-control boxed" value="{{ $villager->name }}" readonly disabled/>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3">
                <label class="control-label" >{{ Lang::get('preylang.villager.list.deviceId') }}</label>
            </div>
            <div class="col-sm-9">
                <input type="text" class="form-control boxed" value="{{ $villager->device_imei }}" autofocus readonly disabled/>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3">
                <label class="control-label" >{{ Lang::get('preylang.login.password') }}</label>
            </div>
            <div class="col-sm-6">
                <input type="password" id="password-id-{{ $villager->id }}" class="read-only" readonly="readonly" value="{{ $villager->password }}" />
                <div class="pull-right">
                    <label class="cursor-pointer">
                        <input type="checkbox" id="toogle-password{{ $villager->id }}" class="toggle-password" data-password-id="{{ $villager->id }}" />
                        &nbsp;{{ Lang::get('preylang.villager.list.showPassword') }}
                    </label>
                </div>
            </div>
            {{--<div class="col-sm-3">--}}
                {{--<label class="align-checkbox cursor-pointer">--}}
                    {{--<input type="checkbox" id="tooglePwd1" class="toggle-password" data-password-id="1" />--}}
                    {{--{{ Lang::get('preylang.villager.list.showPassword') }}--}}
                {{--</label>--}}
            {{--</div>--}}
        </div>
        <div class="form-group row">
            <div class="col-sm-3">
                <label class="control-label" >{{ Lang::get('preylang.userGroup') }}</label>
            </div>
            <div class="col-sm-9">
                <input type="text" class="form-control boxed" value="{{ isset($villager->userGroup) ? Lang::locale() == 'en' ? $villager->userGroup->name : $villager->userGroup->name_kh : '' }}" autofocus readonly disabled/>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3">
                <label class="control-label" >{{ Lang::get('preylang.province') }}</label>
            </div>
            <div class="col-sm-9">
                <input type="text" class="form-control boxed" value="{{ Lang::locale() == 'en' ? $villager->province->name : $villager->province->name_kh }}" autofocus readonly disabled/>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3"></div>
            <div class="col-sm-9">
                <a class="btn btn-secondary" href="{{ route('villager.index') }}">
                    <i class="ace-icon fa fa-caret-left bigger-130" aria-hidden="true"></i>
                    {{ Lang::get('preylang.label.back') }}
                </a>
            </div>
        </div>
    </div>
@endsection
