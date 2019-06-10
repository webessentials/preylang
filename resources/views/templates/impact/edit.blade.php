@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.breadcrumb.impact') }}
@endsection

@section('title')
    {{ Lang::get('preylang.breadcrumb.impact') }}
@endsection

@section('content')
@include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'impact'])
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-block">
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

                <form role="form" method="POST" id="impact-form-fields" action="{{ route('impact.update', $impact->id) }}">
                    @csrf
                    @method('PUT')
                    @include('partials.impact.formFields', ['readOnly' => ''])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
