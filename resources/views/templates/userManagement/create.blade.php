@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.user') }}
@endsection

@section('title')
    {{ Lang::get('preylang.user') }}
@endsection

@section('content')
    @include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'user'])
    <div class="card card-block sameheight-item">
        <div class="title-block">
            <h3 class="title">
                {{ Lang::get('preylang.user.createUser') }}
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
        <form role="form" method="POST" action="{{ route('user.store') }}">
            @csrf
            <div class="form-group row {{ $errors->has('first_name') ? 'has-error' : '' }}">
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.user.list.firstName') }}</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control boxed {{ $errors->has('first_name') ? 'is-invalid' : '' }}" id="first_name" name="first_name" value="{{ old('first_name') }}" autofocus />
                </div>
            </div>
            <div class="form-group row {{ $errors->has('last_name') ? 'has-error' : '' }}">
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.user.list.lastName') }}</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control boxed {{ $errors->has('last_name') ? 'is-invalid' : '' }}" id="last_name" name="last_name" value="{{ old('last_name') }}" autofocus />
                </div>
            </div>
            <div class="form-group row {{ $errors->has('username') ? 'has-error' : '' }}">
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.login.username') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control boxed {{ $errors->has('username') ? 'is-invalid' : '' }}" id="username" name="username" value="{{ old('username') }}" />
                </div>
            </div>
            <div class="form-group row" {{ $errors->has('email') ? 'has-error' : '' }}>
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.user.email') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    <input type="email" class="form-control boxed {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email" name="email" value="{{ old('email') }}" />
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
            <div class="form-group row" {{ $errors->has('role') ? 'has-error' : '' }}>
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.user.role') }}</label>
                </div>
                <div class="col-sm-9">
                    @can('storeAsSuperAdmin', \App\Models\User::class)
                        <select class="form-control boxed" name="role" id="role" data-villager-role="{{ config('settings.villager_role') }}">
                            @foreach(Config('settings.user_roles') as $role)
                                <option value="{{ $role }}" {{ (old('role') == $role ? 'selected' : '') }}>{{ Lang::get('preylang.user.role.' . $role) }}</option>
                            @endforeach
                        </select>
                    @endcan
                    @can('storeByGroup', \App\Models\User::class)
                        <select class="form-control boxed" name="role" id="role" data-villager-role="{{ config('settings.villager_role') }}">
                            @foreach(Config('settings.user_low_level_roles') as $role)
                                <option value="{{ $role }}">{{ Lang::get('preylang.user.role.' . $role) }}</option>
                            @endforeach
                        </select>
                    @endcan
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.userGroup') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    @can('storeAsSuperAdmin', \App\Models\User::class)
                        {!!
                            Form::select(
                                'user_group_id',
                                $userGroups->prepend(['name' => '', 'id' => ''])->pluck('name', 'id'),
                                '',
                                ['id' => 'user_group_id', 'class' => $errors->has('user_group_id') ? 'form-control boxed is-invalid' : 'form-control boxed']
                            )
                        !!}
                    @endcan
                    @can('storeByGroup', \App\Models\User::class)
                        <input type="text" name="user_group_id"  class="form-control boxed" value="{{ $userGroups }}" readonly/>
                    @endcan
                </div>
            </div>
            <div class="form-group row d-none" id="villager-block" {{ $errors->has('villager_id') ? 'has-error' : '' }}>
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.sidebar.villager') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    <select class="form-control boxed" name="villager_id" id="villager_id">
                        @foreach($villagers as $villager)
                            <option value="{{ $villager->id }}">{{ $villager->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row" {{ $errors->has('language') ? 'has-error' : '' }}>
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.userSetting.language') }}</label>
                </div>
                <div class="col-sm-9">
                    <select class="form-control boxed" name="language_key" id="language_key">
                        <option value="en">{{ Lang::get('preylang.userSetting.english') }}</option>
                        <option value="km">{{ Lang::get('preylang.userSetting.khmer') }}</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                    <a class="btn btn-secondary" href="{{ route('user.index') }}">
                        <i class="ace-icon fa fa-ban bigger-130" aria-hidden="true"></i>
                        {{ Lang::get('preylang.abort') }}
                    </a>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> {{ Lang::get('preylang.save') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
