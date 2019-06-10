@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.province') }}
@endsection

@section('title')
    {{ Lang::get('preylang.province') }}
@endsection

@section('content')
    @include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'province'])
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-block">
                    @if(Session::has('success'))
                        <div class="alert alert-success">{{ Session::get('success') }}</div>
                    @endif

                    @if(Session::has('error'))
                        <div class="alert alert-danger">{{ Session::get('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>{{ Lang::get('preylang.englishName') }}</th>
                                <th>{{ Lang::get('preylang.khmerName') }}</th>
                                <th>{{ Lang::get('preylang.setting.form.province.unique.id') }}</th>
                                <th>
                                    <a class="btn btn-primary btn-mini" title="{{ Lang::get('preylang.new') }}" href="{{ route('province.create') }}">+</a>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @isset($provinces)
                                @foreach($provinces as $province)
                                    <tr>
                                        <td>{{ $province->name }}</td>
                                        <td>{{ $province->name_kh }}</td>
                                        <td>{{ $province->sys_value }}</td>
                                        <td>
                                            <a class="btn green" title="{{ Lang::get('preylang.edit') }}" href="{{ route('province.edit', $province->id) }}">
                                                <i class="ace-icon fa fa-pencil bigger-130" aria-hidden="true"></i>
                                            </a>

                                            @if($province->villagers->count() === 0)
                                                <a href="#" class="btn green" title="{{ Lang::get('preylang.delete') }}" data-toggle="modal" data-target="#deleteUserModal-{{ $province->id }}">
                                                    <i class="ace-icon fa fa-trash-o bigger-130" aria-hidden="true"></i>
                                                </a>
                                            @else
                                                <a href="#" class="btn green disabled" title="{{ Lang::get('preylang.delete') }}">
                                                    <i class="ace-icon fa fa-trash-o bigger-130" aria-hidden="true"></i>
                                                </a>
                                            @endif
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
    @foreach($provinces as $province)
        @if($province->villagers->count() === 0)
            @include('partials.modal', ['show'=>$province, 'id'=>'deleteUser', 'path'=>'province.delete'])
        @endif
    @endforeach
@endsection
