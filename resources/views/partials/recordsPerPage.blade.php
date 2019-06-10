<form id="form-record-per-page" class="form-inline form-process">
    <div class="form-group">
        <label>{{ Lang::get('preylang.display') }}</label>
        <select class="form-control boxed mx-sm-3" id="record-per-page" name="perpage">
            @foreach(Config('settings.records_per_page') as $record_per_page)
                <option value="{{ $record_per_page }}" {{ (int)$recordsPerPage === (int)$record_per_page ? 'selected="selected"' : '' }}>
                    {{ $record_per_page }}
                </option>
            @endforeach
        </select>
        <label>{{ Lang::get('preylang.record') }}</label>
    </div>
</form>
