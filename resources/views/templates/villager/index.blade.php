@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.sidebar.villager') }}
@endsection

@section('title')
    {{ Lang::get('preylang.sidebar.villager') }}
@endsection

@section('content')
    @include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'villager'])
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
                                <th>@sortablelink('name', trans('preylang.villager.list.villagerId'))</th>
                                <th>@sortablelink('device_imei', trans('preylang.villager.list.deviceId'))</th>
                                <th>{{ Lang::get('preylang.userGroup') }}</th>
                                <th>@sortablelink('province.name', trans('preylang.province'))</th>
                                <th>{{ Lang::get('preylang.login.password') }}</th>
                                <th>
                                    @can('interactWithButtons', \App\Models\Villager::class)
                                        <a class="btn btn-primary btn-mini" title="{{ Lang::get('preylang.new') }}" href="{{ route('villager.create') }}">+</a>
                                    @endcan
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($villagers as $villager)
                                <tr>
                                    <td>{{ $villager->name }}</td>
                                    <td>{{ $villager->device_imei }}</td>
                                    <td>{{ $villager->userGroup ? Lang::locale() == 'en' ? $villager->userGroup->name : $villager->userGroup->name_kh : '' }}</td>
                                    <td>{{ Lang::locale() == 'en' ? $villager->province->name : $villager->province->name_kh }}</td>
                                    <td>
                                        <input type="password" id="password-id-{{ $villager->id }}" class="read-only" readonly="readonly" value="{{ $villager->password }}" />
                                        <div class="pull-right">
                                            <label class="cursor-pointer">
                                                <input type="checkbox" id="toogle-password{{ $villager->id }}" class="toggle-password" data-password-id="{{ $villager->id }}" />
                                                &nbsp;{{ Lang::get('preylang.villager.list.showPassword') }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <a class="btn green" title="{{ Lang::get('preylang.show') }}" href="{{ route('villager.show',$villager->id) }}">
                                            <i class="ace-icon fa fa-eye bigger-130" aria-hidden="true"></i>
                                        </a>
                                        @can('interactWithButtons', \App\Models\Villager::class)
                                            <a class="btn green" title="{{ Lang::get('preylang.edit') }}" href="{{ route('villager.edit',$villager->id) }}">
                                                <i class="ace-icon fa fa-pencil bigger-130" aria-hidden="true"></i>
                                            </a>
                                            <a href="#" class="btn green" title="{{ Lang::get('preylang.delete') }}" data-toggle="modal" data-target="#deleteVillagerModal-{{ $villager->id }}">
                                                <i class="ace-icon fa fa-trash-o bigger-130" aria-hidden="true"></i>
                                            </a>
                                        @endcan
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
    @include('partials.totalRecord', ['start' => $villagers->firstItem(), 'end' => $villagers->lastItem(), 'total' => $villagers->total()])
    @foreach($villagers as $villager)
        @include('partials.modal', ['show'=>$villager, 'id'=>'deleteVillager', 'path'=>'villager.delete'])
    @endforeach
    {!! $villagers->appends(\Request::except('page'))->render() !!}
@endsection
