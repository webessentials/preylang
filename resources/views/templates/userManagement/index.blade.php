@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.user') }}
@endsection

@section('title')
    {{ Lang::get('preylang.user') }}
@endsection

@section('content')
@include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'user'])
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-block">
                <div class="row">
                    <div class="col-md-6">
                        @include("partials.recordsPerPage", ['recordsPerPage' => $recordsPerPage])
                    </div>
                    <div class="col-md-6">
                        <div class="pull-right">
                            @include("partials.searchBox", ['keyword' => $keyword])
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    @if(Session::has('success'))
                        <div class="alert alert-success">{{ Session::get('success') }}</div>
                    @endif
                    @if(Session::has('error'))
                        <div class="alert alert-danger">{{ Session::get('error') }}</div>
                    @endif
                    <table class="table table-hover table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>{{ Lang::get('preylang.user.list.firstName') }}</th>
                            <th>{{ Lang::get('preylang.user.list.lastName') }}</th>
                            <th>{{ Lang::get('preylang.login.username') }}</th>
                            <th>{{ Lang::get('preylang.user.role') }}</th>
                            <th>{{ Lang::get('preylang.userGroup') }}</th>
                            <th>{{ Lang::get('preylang.user.email') }}</th>
                            <th>
                                <a class="btn btn-primary btn-mini" title="{{ Lang::get('preylang.new') }}" href="{{ route('user.create') }}">+</a>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ Lang::get('preylang.user.role.' . $user->role) }}</td>
                                <td>{{ isset($user->userGroup->name) ? $user->userGroup->name : ''}}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <a class="btn green" title="{{ Lang::get('preylang.edit') }}" href="{{ route('user.edit',$user->id) }}">
                                        <i class="ace-icon fa fa-pencil bigger-130" aria-hidden="true"></i>
                                    </a>
                                    @if($user->id != Auth::User()->id)
                                    <a href="#" class="btn green" title="{{ Lang::get('preylang.delete') }}" data-toggle="modal" data-target="#deleteUserModal-{{ $user->id }}">
                                        <i class="ace-icon fa fa-trash-o bigger-130" aria-hidden="true"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@foreach($users as $user)
    @include('partials.modal', ['show'=>$user, 'id'=>'deleteUser', 'path'=>'user.delete'])
@endforeach
@include('partials.totalRecord', ['start' => $users->firstItem(), 'end' => $users->lastItem(), 'total' => $users->total()])
{!! $users->appends(\Request::except('page'))->render() !!}
@endsection
