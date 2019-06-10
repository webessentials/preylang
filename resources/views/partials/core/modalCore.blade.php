@yield("icon")
<div id="impact-{{ $identifier }}" class="modal fade" role="dialog">
    <div class="modal-dialog {{ $modalClass }}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @yield("body")
            </div>
            @if($isFooterEnabled)
                <div class="modal-footer">
                    <button data-toggle="modal" data-target="#impact-{{ $identifier }}" type="button" class="btn btn-primary">{{ $cancelLabel }}</button>
                    @yield("button")

                </div>
            @endif

        </div>
    </div>
</div>
