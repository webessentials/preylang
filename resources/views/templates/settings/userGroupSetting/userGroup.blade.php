@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.userGroup') }}
@endsection

@section('title')
    {{ Lang::get('preylang.userGroup') }}
@endsection

@section('content')
    @include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'usergroups'])
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-block">
                    @if(Session::has('success'))
                        <div class="alert alert-success">{{ Session::get('success') }}</div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>{{ Lang::get('preylang.englishName') }}</th>
                                <th>{{ Lang::get('preylang.khmerName') }}</th>
                                <th>
                                    <a class="btn btn-primary btn-mini" title="{{ Lang::get('preylang.new') }}" href="{{ route('userGroups.create') }}">+</a>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @isset($userGroups)
                                @foreach($userGroups as $userGroup)
                                    <tr>
                                        <td>{{ $userGroup->name }}</td>
                                        <td>{{ $userGroup->name_kh }}</td>
                                        <td>
                                            <a class="btn green" title="{{ Lang::get('preylang.edit') }}" href="{{ route('userGroups.edit', $userGroup->id) }}">
                                                <i class="ace-icon fa fa-pencil bigger-130" aria-hidden="true"></i>
                                            </a>
                                            @if($userGroup->users->count() === 0 && $userGroup->villagers->count() === 0)
                                                <a href="#" class="btn green" title="{{ Lang::get('preylang.delete') }}" data-toggle="modal" data-target="#deleteUserModal-{{ $userGroup->id }}">
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
    @include('partials.totalRecord', ['start' => $userGroups->firstItem(), 'end' => $userGroups->lastItem(), 'total' => $userGroups->total()])
    @foreach($userGroups as $userGroup)
        @if($userGroup->users->count() === 0 && $userGroup->villagers->count() === 0)
            @include('partials.modal', ['show'=>$userGroup, 'id'=>'deleteUser', 'path'=>'userGroups.delete'])
        @endif
    @endforeach
@endsection
