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
                {{ Lang::get('preylang.user.editUser') }}
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
        <form role="form" method="POST" action="{{ route('user.update', $user->id) }}">
            @csrf
            @method('PUT')
            <div class="form-group boxed row {{$errors->has('first_name') ? 'has-error' : ''}}">
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.user.list.firstName') }}</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control boxed {{ $errors->has('first_name') ? 'is-invalid' : '' }}" id="first_name" name="first_name" value="{{ $errors->has('first_name') ? old('first_name') : $user->first_name }}" autofocus />
                </div>
            </div>
            <div class="form-group boxed row {{$errors->has('last_name') ? 'has-error' : ''}}">
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.user.list.lastName') }}</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control boxed {{ $errors->has('last_name') ? 'is-invalid' : '' }}" id="last_name" name="last_name" value="{{ $errors->has('last_name') ? old('last_name') : $user->last_name }}" autofocus />
                </div>
            </div>
            <div class="form-group row {{$errors->has('username') ? 'has-error' : ''}}">
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.login.username') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control boxed {{ $errors->has('username') ? 'is-invalid' : '' }}" id="username" name="username" value="{{ $errors->has('username') ? old('username') : $user->username }}" disabled />
                </div>
            </div>
            <div class="form-group boxed row" {{$errors->has('email') ? 'has-error' : ''}}>
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.user.email') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    <input type="email" class="form-control boxed {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email" name="email" value="{{ $errors->has('email') ? old('email') : $user->email }}" />
                </div>
            </div>
            <div class="form-group row" {{$errors->has('password') ? 'has-error' : ''}}>
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.login.password') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    <input type="password" class="form-control boxed {{$errors->has('password') ? 'is-invalid' : ''}}" id="new_password" name="password" value="{{ $errors->has('password') ? old('password') : '' }}" />
                    <span class="badge-info prompt-message">{{ Lang::get('preylang.changePasswordPrompt') }}</span>
                </div>
            </div>
            <div class="form-group row" {{ $errors->has('role') ? 'has-error' : '' }}>
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.user.role') }}</label>
                </div>
                <div class="col-sm-9">
                    @can('updateAsSuperAdmin', \App\Models\User::class)
                        <select class="form-control boxed" name="role" id="role" {{ Auth::user()->id == $user->id ? 'disabled' : '' }} data-villager-role="{{ config('settings.villager_role') }}">
                            @foreach(Config('settings.user_roles') as $role)
                                <option value="{{ $role }}" {{ (old('role') == $role ? 'selected' : '') }} {{ $user->role == $role ? 'selected' : '' }}>{{ Lang::get('preylang.user.role.' . $role) }}</option>
                            @endforeach
                        </select>
                    @endcan
                    @can('updateByGroup', $user)
                        <select class="form-control boxed" name="role" id="role" {{ Auth::user()->id == $user->id ? 'disabled' : '' }} data-villager-role="{{ config('settings.villager_role') }}">
                            @foreach(Config('settings.user_low_level_roles') as $role)
                                <option value="{{ $role }}" {{ $user->role == $role ? 'selected' : '' }}>{{ Lang::get('preylang.user.role.' . $role) }}</option>
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
                    @can('updateAsSuperAdmin', \App\Models\User::class)
                        {!!
                            Form::select(
                                'user_group_id',
                                $userGroups->prepend(['name' => '', 'id' => ''])->pluck('name', 'id'),
                                $user->user_group_id,
                                [
                                'id' => 'user_group_id',
                                'class' => $errors->has('user_group_id') ? 'form-control boxed is-invalid' : 'form-control boxed',
                                in_array($user->role, config('settings.user_super_roles')) ? 'disabled' : ''
                                ]
                            )
                        !!}
                    @endcan
                    @can('updateByGroup', $user)
                        <input type="text" name="user_group_id" class="form-control boxed" value="{{ $userGroups }}" readonly />
                    @endcan
                </div>
            </div>
            <div class="form-group row {{ $user->role !== 'patroller' ? 'd-none' : '' }}" id="villager-block" {{ $errors->has('villager_id') ? 'has-error' : '' }}>
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.sidebar.villager') }}</label>
                    <label class="text-danger">*</label>
                </div>
                <div class="col-sm-9">
                    <select class="form-control boxed" name="villager_id" id="villager_id">
                        @foreach($villagers as $villager)
                            <option value="{{ $villager->id }}" {{ $user->villager_id == $villager->id ? 'selected' : '' }}>{{ $villager->name }}</option>
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
                        <option value="en" {{ $user->language_key == 'en' ? 'selected' : '' }}>{{ Lang::get('preylang.userSetting.english') }}</option>
                        <option value="km" {{ $user->language_key == 'km' ? 'selected' : '' }}>{{ Lang::get('preylang.userSetting.khmer') }}</option>
                    </select>
                </div>
            </div>
            <div class="form-group row" {{ $errors->has('active') ? 'has-error' : '' }}>
                <div class="col-sm-3">
                    <label class="control-label" >{{ Lang::get('preylang.user.active') }}</label>
                </div>
                <div class="col-sm-9">
                    <label>
                        <input class="form-control checkbox rounded" name="active" id="active" {{ Auth::user()->id == $user->id ? 'disabled' : '' }} {{ $user->active == 1 ? 'checked' : '' }} type="checkbox" value="1">
                        <span></span>
                    </label>
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
