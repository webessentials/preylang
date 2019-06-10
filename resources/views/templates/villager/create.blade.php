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
                {{ Lang::get('preylang.villager.createVillager') }}
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
        <form role="form" method="POST" action="{{ route('villager.store') }}">
            @csrf
            <div class="form-group row {{ $errors->has('device_imei') ? 'has-error' : '' }}">
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.villager.list.deviceId') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control boxed {{ $errors->has('device_imei') ? 'is-invalid' : '' }}" id="device-imei" name="device_imei" value="{{ old('device_imei') }}" autofocus />
                </div>
            </div>
            <div class="form-group row" {{ $errors->has('password') ? 'has-error' : '' }}>
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.login.password') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    <input type="password" class="form-control boxed {{ $errors->has('password') ? 'is-invalid' : '' }}" id="password" name="password" value="{{ old('password') }}" />
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.userGroup') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    @can('storeAsSuperAdmin', \App\Models\Villager::class)
                        {!!
                            Form::select(
                                'user_group_id',
                                $userGroups->prepend(['name' => '', 'id' => ''])->pluck('name', 'id'),
                                '',
                                ['id' => 'user_group_id', 'class' => $errors->has('user_group_id') ? 'form-control boxed is-invalid' : 'form-control boxed']
                            )
                        !!}
                    @endcan

                    @can('storeByGroup', \App\Models\Villager::class)
                        <input type="text" class="form-control boxed" value="{{ $userGroups }}" disabled/>
                    @endcan
                </div>
            </div>
            <div class="form-group row" {{ $errors->has('province_id') ? 'has-error' : '' }}>
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.province') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    <select class="form-control boxed" name="province_id" id="province-id">
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}">{{ Lang::locale() == 'en' ? $province->name : $province->name_kh }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                    <a class="btn btn-secondary" href="{{ route('villager.index') }}">
                        <i class="ace-icon fa fa-ban bigger-130" aria-hidden="true"></i>
                        {{ Lang::get('preylang.abort') }}
                    </a>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> {{ Lang::get('preylang.save') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
