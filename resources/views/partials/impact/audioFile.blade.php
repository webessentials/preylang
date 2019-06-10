@extends('partials.core.modalCore', ['identifier' => $identifier, 'isFooterEnabled' => true, 'cancelLabel' => __('preylang.label.close'), 'title' => __('preylang.impact.modalAudioTitle'), 'modalClass' => ''])
@section("icon")
    <a data-toggle="modal" data-target="#impact-{{ $identifier }}">
        <i class="ace-icon fa fa-file-audio-o"></i>
    </a>
@overwrite
@section("body")
    <p>{{ __('preylang.impact.clickToPlaySoundRecord') }}</p>
    @foreach($files as $file)
        <audio class="file-audio" controls="controls">
            <source src="{{ route('files.get', $file->file_name) }}" type="audio/mpeg">
        </audio>
    @endforeach
@overwrite
