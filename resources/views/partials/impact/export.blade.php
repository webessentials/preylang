<form class="form-inline form-process" method="POST" action="{{ route('impact.export', ['filter' => $searchFields]) }}">
    @csrf
    <div class="form-group">
        <label for="email">
            {{ Lang::get('preylang.impact.label.exportInstruction') }}
        </label>
        <input type="text" id="email" class="form-control boxed mx-sm-3 {{ $errors->has('email') ? 'is-invalid' : '' }}" name="email" value="{{ Session::has('old_email') ? Session::get('old_email') : Auth::user()->email }}" placeholder="{{ Lang::get('preylang.impact.placeholder.email') }}"/>
        <input type="submit" class="btn {{ $impacts->total() > 0 ? 'btn-primary' : 'btn-disable' }}" name="type_CSV" value="{{ Lang::get('preylang.impact.label.exportToCSV') }}" {{ $impacts->total() == 0 ? 'disabled' : '' }}/>
        <input type="submit" class="btn {{ $impacts->total() > 0 ? 'btn-primary' : 'btn-disable' }}" name="type_Excel" value="{{ Lang::get('preylang.impact.label.exportToExcel') }}" {{ $impacts->total() == 0 ? 'disabled' : '' }}/>
    </div>
    {{ Session::forget('old_email') }}
</form>
