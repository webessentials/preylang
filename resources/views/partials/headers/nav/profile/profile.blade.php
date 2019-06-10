<li class="profile dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
    <span class="name">
        {{ Auth::user()->username }}
    </span>
    </a>
    <div class="dropdown-menu profile-dropdown-menu" aria-labelledby="dropdownMenu1">
        <a class="dropdown-item" href="#">
            <i class="fa fa-user icon"></i>
            {{ Lang::get('preylang.userSetting.profile') }}
        </a>
        <a class="dropdown-item" href="{{ route('user.userSetting') }}">
            <i class="fa fa-gear icon"></i>
            {{ Lang::get('preylang.userSetting') }}
        </a>
        <div class="dropdown-divider" ></div>
        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-power-off icon"></i>
            {{ Lang::get('preylang.application.logout') }}
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</li>
