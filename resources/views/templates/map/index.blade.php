@extends('layouts.app')

@section('pageTitle')
{{ Lang::get('preylang.breadcrumb.map') }}
@endsection

@section('title')
{{ Lang::get('preylang.breadcrumb.map') }}
@endsection

@section('content')
    @include('partials.breadcrumb.breadcrumb', ['breadcrumb' => 'map'])
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-block">
                    <div>@include('partials.map.filters')</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card impact-map-card">
                <div class="card-block">
                    <div id="impactMap" data-url="{{ route('map.impacts') }}"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
