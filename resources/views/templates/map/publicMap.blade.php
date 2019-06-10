@extends('layouts.public')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div id="public-map-filter">@include('partials.map.publicFilters')</div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="impact-map-card">
            <div id="impactMap" data-url="{{ route('public.map.impacts') }}"></div>
        </div>
    </div>
</div>
@endsection
