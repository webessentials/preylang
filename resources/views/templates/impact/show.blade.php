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
            <div class="card-block read-only" id="impact-form-fields">
                @include('partials.impact.formFields', ['readOnly' => 'readonly'])
            </div>
        </div>
    </div>
</div>
@endsection
