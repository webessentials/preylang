@extends('partials.core.modalCore', ['identifier' => $identifier, 'isFooterEnabled' => true, 'cancelLabel' => __('preylang.label.close'), 'title' => __('preylang.impact.modalMapTitle'), 'modalClass' => 'map-modal'])
@section("icon")
    <a data-toggle="modal" data-target="#impact-{{ $identifier }}" onclick="setMap('mapid-{{ $identifier }}', {{ $latitude }}, {{ $longitude }})">
        <i class="fa fa-map-marker bigger-140 icon-status-large icon-location-map"></i>
    </a>
@overwrite

@section("body")
    <div id="mapid-{{ $identifier }}" style="width: 100%; height: 380px;"></div>
    <table class="map-modal-location-container">
        <tr>
            <td>{{ __('preylang.impact.mapLatitude') }} : <b>{{ $latitude }}</b></td>
            <td>{{ __('preylang.impact.mapLongitude') }} : <b>{{ $longitude }}</b></td>
        </tr>
    </table>
@overwrite
