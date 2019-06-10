<?php
use App\Helpers\CategoryHelper;
$categories = CategoryHelper::getCategoriesByLevel();
?>
<aside class="sidebar">
    <div class="sidebar-container">
        @include('partials.sidebar.header.header')
        <nav class="menu">
            <ul class="sidebar-menu">
                <li class="{{ Request::is('/') || Request::is('dashboard') ? 'active' : '' }}" >
                    <a href="/">
                        <i class="fa fa-tachometer"></i>
                        <span>{{ Lang::get('preylang.dashboard') }}</span>
                    </a>
                </li>
                <li class="open {{ Request::is('user') || Request::is('user/*')
                                || Request::is('setting') || Request::is('setting/*')
                                || Request::is('villager') || Request::is('villager/*')
                                || Request::is('impact') || Request::is('impact/*')
                                || Request::is('rawimpact') || Request::is('rawimpact/*')
                                || Request::is('activity') || Request::is('activity/*')
                                || Request::is('map') || Request::is('map/*')
                                 ? 'active' : '' }}">
                    <a href="#sidebar-menu" data-target="#sidebar-menu" data-toggle="collapse" aria-expanded="false" aria-controls="#sidebar-menu" class="btn-content-toggle">
                        <i class="fa fa-th-large"></i>
                        <span>{{ Lang::get('preylang.sidebar.content') }}</span>
                        <i class="fa arrow"></i>
                    </a>
                    <ul class="sidebar-nav collapse show" style="" id="sidebar-menu">
                        @can('interact', \App\Models\Impact::class)
                            <li class="{{ Request::is('activity') || Request::is('activity/*') ? 'active' : '' }}">
                                <a href="{{ route('activity.index') }}">
                                    <i class="fa fa-list fa-fw"></i> {{ Lang::get('preylang.sidebar.activities') }}
                                </a>
                            </li>
                        @endcan
                        @can('interact', \App\Models\Impact::class)
                            <li class="{{ Request::is('map') || Request::is('map/*') ? 'active' : '' }}">
                                <a href="{{ route('map') }}">
                                    <i class="fa fa-map-marker fa-fw"></i> {{ Lang::get('preylang.sidebar.map') }}
                                </a>
                            </li>
                        @endcan
                        @can('viewListForAllGroups', \App\Models\Setting::class)
                            <li class="{{ Request::is('setting/*') ? 'open' : '' }}">
                                <a href="#setting-menu" data-target="#setting-menu" data-toggle="collapse" aria-expanded="false" aria-controls="#setting-menu" class="btn-content-toggle{{ Request::is('setting/*') ? '' : ' collapsed' }} ">
                                    <i class="fa fa-gear fa-fw"></i> {{ Lang::get('preylang.setting') }}
                                    <i class="fa arrow"></i>
                                </a>
                                <ul class="sidebar-nav collapse {{ Request::is('setting/*') ? 'show' : '' }}" id="setting-menu">
                                    @can('viewListSuperAdmin', \App\Models\Setting::class)
                                        <li class="{{ Request::is('setting/province') || Request::is('setting/province/*') ? 'active' : '' }}">
                                            <a href="{{ route('province.index') }}">
                                                {{ Lang::get('preylang.province') }}
                                            </a>
                                        </li>
                                        <li class="{{ Request::is('setting/usergroups') || Request::is('setting/usergroups/*') ? 'active' : '' }}">
                                            <a href="{{ route('userGroups.index') }}">
                                                {{ Lang::get('preylang.userGroup') }}
                                            </a>
                                        </li>
                                    @endcan
                                    @can('viewListSuperDataManager', \App\Models\Setting::class)
                                        <li class="{{ Request::is('setting/category') || Request::is('setting/category/*') ? 'active' : '' }}">
                                            <a href="{{ route('category.index') }}">
                                                {{ Lang::get('preylang.breadcrumb.category') }}
                                            </a>
                                        </li>
                                        <li class="{{ Request::is('setting/proof') || Request::is('setting/proof/*') ? 'active' : '' }}">
                                            <a href="{{ route('proof.index') }}">
                                                {{ Lang::get('preylang.setting.proof') }}
                                            </a>
                                        </li>
                                        <li class="{{ Request::is('setting/reason') || Request::is('setting/reason/*') ? 'active' : '' }}">
                                            <a href="{{ route('reason.index') }}">
                                                {{ Lang::get('preylang.setting.reason') }}
                                            </a>
                                        </li>
                                        <li class="{{ Request::is('setting/offender') || Request::is('setting/offender/*') ? 'active' : '' }}">
                                            <a href="{{ route('offender.index') }}">
                                                {{ Lang::get('preylang.setting.offender') }}
                                            </a>
                                        </li>
                                        <li class="{{ Request::is('setting/victim_type') || Request::is('setting/victim_type/*') ? 'active' : '' }}">
                                            <a href="{{ route('victimType.index') }}">
                                                {{ Lang::get('preylang.setting.victimType') }}
                                            </a>
                                        </li>
                                        <li class="{{ Request::is('setting/designation') || Request::is('setting/designation/*') ? 'active' : '' }}">
                                            <a href="{{ route('designation.index') }}">
                                                {{ Lang::get('preylang.setting.designation') }}
                                            </a>
                                        </li>
                                        <li class="{{ Request::is('setting/threatening') || Request::is('setting/threatening/*') ? 'active' : '' }}">
                                            <a href="{{ route('threatening.index') }}">
                                                {{ Lang::get('preylang.setting.threatening') }}
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                        @can('interact', \App\Models\Villager::class)
                            <li class="{{ Request::is('villager') || Request::is('villager/*') ? 'active' : '' }}">
                                <a href="{{ route('villager.index') }}">
                                    <i class="fa fa-users fa-fw"></i> {{ Lang::get('preylang.sidebar.villager') }}
                                </a>
                            </li>
                        @endcan
                        @can('interact', \App\Models\Impact::class)
                            <li class="{{ Request::is('impact') || Request::is('impact/filter') ? 'open' : '' }}">
                                <a href="#impact-menu" data-target="#impact-menu" data-toggle="collapse" aria-expanded="false" aria-controls="#impact-menu" class="btn-content-toggle{{ Request::is('impact/*') ? '' : ' collapsed' }} ">
                                    <i class="fa fa-th-large fa-fw"></i> {{ Lang::get('preylang.sidebar.impact') }}
                                    <i class="fa arrow"></i>
                                </a>
                                <ul class="sidebar-nav collapse {{ Request::is('impact/*') || Request::is('impact') ? 'show' : '' }}" id="impact-menu">
                                    <li class="{{ Request::is('impact') || Request::is('impact/filter') ? 'active' : '' }}">
                                        <a href="{{ route('impact.index') }}" class="btn-impact-toggle {{ Request::is('impact/*') ? '' : '' }} ">
                                            {{ Lang::get('preylang.impact.filter.all') }}
                                        </a>
                                    </li>
                                    @foreach($categories as $key => $value)
                                    <li class="{{ Request::is('impact/filterByCategory/'.$key) ? 'active' : '' }}">
                                        <a href="{{ route('impact.filterByCategory', $key) }}"> {{$value}} </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endcan
                        @can('interact', \App\Models\RawImpact::class)
                            <li class="{{ Request::is('rawimpact') || Request::is('rawimpact/*') ? 'open' : '' }}">
                                <a href="#rawimpact-menu" data-target="#rawimpact-menu" data-toggle="collapse" aria-expanded="false" aria-controls="#rawimpact-menu" class="btn-content-toggle{{ Request::is('rawimpact/*') ? '' : ' collapsed' }} ">
                                    <i class="fa fa-list-alt fa-fw"></i> {{ Lang::get('preylang.sidebar.rawImpact') }}
                                    <i class="fa arrow"></i>
                                </a>
                                <ul class="sidebar-nav collapse {{ Request::is('rawimpact/*') || Request::is('rawimpact') ? 'show' : '' }}" id="rawimpact-menu">
                                    <li class="{{ Request::is('rawimpact') ? 'active' : '' }}">
                                        <a href="{{ route('rawImpact.index') }}" class="btn-rawimpact-toggle {{ Request::is('rawimpact/*') ? '' : '' }} ">
                                            {{ Lang::get('preylang.impact.filter.all') }}
                                        </a>
                                    </li>
                                    @foreach($categories as $key => $value)
                                    <li class="{{ Request::is('rawimpact/filterByCategory/'.$key) ? 'active' : '' }}">
                                        <a href="{{ route('rawImpact.filterByCategory', $key) }}"> {{$value}} </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endcan
                        @can('interact', \App\Models\User::class)
                            <li class="{{ Request::is('user') || Request::is('user/*') ? 'active' : '' }}">
                                <a href="{{ route('user.index') }}">
                                    <i class="fa fa-user fa-fw"></i> {{ Lang::get('preylang.user') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            </ul>
            <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
                <a title="Click to collaspe menu" >
                    <i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right" aria-hidden="true"></i>
                </a>
            </div>
        </nav>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebar-overlay"></div>
<div class="sidebar-mobile-menu-handle" id="sidebar-mobile-menu-handle"></div>
