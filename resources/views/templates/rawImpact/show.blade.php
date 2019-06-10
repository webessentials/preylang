@extends('layouts.app')

@section('pageTitle')
    {{ Lang::get('preylang.breadcrumb.rawImpact') }}
@endsection

@section('title')
    {{ Lang::get('preylang.breadcrumb.rawImpact') }}
@endsection

@section('content')
@include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'rawImpact'])
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-block">
                @include('partials.rawImpact.formFields', ['readOnly' => 'readonly'])
            </div>
        </div>
    </div>
</div>
@endsection
