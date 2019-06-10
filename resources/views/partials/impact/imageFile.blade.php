@extends('partials.core.modalCore', ['identifier' => $identifier, 'isFooterEnabled' => true, 'cancelLabel' => __('preylang.label.close'), 'title' => __('preylang.impact.modalImageTitle'), 'modalClass' => 'modal-image'])
@section("icon")
    @if(isset($displayThumbnail))
        @foreach($files as $file)
            <div class="col-sm-3">
                <a data-toggle="modal" data-target="#impact-{{ $identifier }}">
                    <img src="{{ route('files.get', $file->file_name) }}" class="img-thumbnail"/>
                </a>
            </div>
        @endforeach
    @else
        <a data-toggle="modal" data-target="#impact-{{ $identifier }}">
            <i class="ace-icon fa fa-file-image-o"></i>
        </a>
    @endif
@overwrite
@section("body")
    <div id="carouselImageImpact{{ $identifier }}" class="carousel slide" data-ride="carousel" data-interval="false">
        <div class="carousel-inner">
            @foreach($files as $file)
                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                    <img src="{{ route('files.get', $file->file_name) }}" alt="{{ $file->file_name }}"/>
                </div>
            @endforeach
        </div>
        @if(count($files) > 1)
            <a class="carousel-control-prev" href="#carouselImageImpact{{ $identifier }}" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">{{ __('preylang.pagination.next') }}</span>
            </a>
            <a class="carousel-control-next" href="#carouselImageImpact{{ $identifier }}" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">{{ __('preylang.pagination.prev') }}</span>
            </a>
        @endif
    </div>
@overwrite
