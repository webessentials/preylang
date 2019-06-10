<form class="form-inline form-process">
    <div class="form-group">
        <label for="select-impact-action">{{ Lang::get('preylang.label.action') }}</label>
        <select class="form-control mx-sm-3 boxed" id="select-impact-action">
            <option value="exclude">{{ Lang::get('preylang.impact.field.exclude') }}</option>
            <option value="include">{{ Lang::get('preylang.impact.field.include') }}</option>
            {{-- uncomment the following two lines to enable export of selected impacts --}}
            {{-- <option value="export-to-CSV">{{ Lang::get('preylang.impact.label.exportToCSV') }}</option> --}}
            {{-- <option value="export-to-Excel">{{ Lang::get('preylang.impact.label.exportToExcel') }}</option> --}}
        </select>
        <a class="btn btn-primary" href="#" id="btn-impact-process" data-msg-confirm="{{ Lang::get('preylang.modal.message.body.process') }}" data-msg-error="{{ Lang::get('preylang.impact.modal.process.selectImpact') }}">{{ Lang::get('preylang.label.process') }}</a>
    </div>
</form>
