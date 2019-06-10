<div class="modal fade" id="{{ $id }}Modal-{{ $show->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ Lang::get('preylang.modal.deleteMessage') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ Lang::get('preylang.modal.confirm') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ Lang::get('preylang.modal.button.no') }}</button>

                <form method="POST" id="{{ $id }}-{{ $show->id }}" action="{{ route($path,$show->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-primary" type="submit">{{ Lang::get('preylang.modal.button.yes') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
