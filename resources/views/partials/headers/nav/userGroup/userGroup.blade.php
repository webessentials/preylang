@can('viewListForAllGroups', \App\Models\Impact::class)
    @if (GlobalFilterHelper::showFilter(request()->route()->getName()))
        <form class="form-inline global-usergroup-filter">
            <div class="form-group">
                <label for="userGroup">{{ Lang::get('preylang.userGroup') }}:</label>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span>{{ $userGroupData['currentFiteredUserGroup'] }}</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('setGlobalUserGroup', ['id' => ''])}}">{{ Lang::get('preylang.label.all') }}</a>
                        @foreach($userGroupData['userGroups'] as $userGroup)
                        <a class="user-group dropdown-item" href="{{route('setGlobalUserGroup', ['id' => $userGroup['id']])}}">
                            {{ Lang::locale() == 'en' ? $userGroup['name'] : $userGroup['name_kh'] }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
    @endif
@endcan
