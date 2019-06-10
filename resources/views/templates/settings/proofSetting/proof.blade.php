@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.setting.proof') }}
@endsection

@section('title')
    {{ Lang::get('preylang.setting.proof') }}
@endsection

@section('content')
    @include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'proof'])
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
                                <th>
                                    <a class="btn btn-primary btn-mini" title="{{ Lang::get('preylang.new') }}" href="{{ route('proof.create') }}">+</a>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @isset($proofs)
                                @foreach($proofs as $proof)
                                    <tr>
                                        <td>{{ $proof->name }}</td>
                                        <td>
                                            @if(!$proof->read_only)
                                                <a class="btn green" title="{{ Lang::get('preylang.edit') }}" href="{{ route('proof.edit', $proof->id) }}">
                                                    <i class="ace-icon fa fa-pencil bigger-130" aria-hidden="true"></i>
                                                </a>
                                            @else
                                                <a class="btn green disabled" title="{{ Lang::get('preylang.edit') }}">
                                                    <i class="ace-icon fa fa-pencil bigger-130" aria-hidden="true"></i>
                                                </a>
                                            @endif

                                            @if($proof->proofImpacts->count() === 0)
                                                <a href="#" class="btn green" title="{{ Lang::get('preylang.delete') }}" data-toggle="modal" data-target="#deleteUserModal-{{ $proof->id }}">
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
    @foreach($proofs as $proof)
        @if($proof->proofImpacts->count() === 0)
            @include('partials.modal', ['show'=>$proof, 'id'=>'deleteUser', 'path'=>'proof.delete'])
        @endif
    @endforeach
@endsection
