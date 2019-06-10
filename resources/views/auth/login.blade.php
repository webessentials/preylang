@extends('layouts.login')

@section('pageTitle')
    {{ Lang::get('preylang.login') }}
@endsection

@section('content')
    <div class="auth">
        <div class="auth-container">
            <div class="card">
                <header class="auth-header">
                    <h1 class="auth-title">
                        <div class="logo"></div>
                        {{ Lang::get('preylang.loginTitle') }}
                    </h1>
                </header>
                <div class="auth-content">
                    <form id="login-form"  action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="username" >{{ Lang::get('preylang.login.username') }}</label>
                            <input id="username" type="text" class="form-control underlined{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" placeholder="{{ Lang::get('preylang.login.usernamePlaceholder') }}" required autofocus>

                            @if ($errors->has('username'))
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('username') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="password" >{{ Lang::get('preylang.login.password') }}</label>
                            <input id="password" type="password" class="form-control underlined{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ Lang::get('preylang.login.passwordPlaceholder') }}" required>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="remember">
                                <input class="checkbox" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span>{{ Lang::get('preylang.login.remember') }}</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-block btn-primary">{{ Lang::get('preylang.login') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
