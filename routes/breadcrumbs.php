<?php
Breadcrumbs::register('home', function ($breadcrumbs) {
    $breadcrumbs->push(Lang::get('preylang.breadcrumb.home'), route('dashboard'));
    $breadcrumbs->push(Lang::get('preylang.dashboard'), route('dashboard'));
});

//user
Breadcrumbs::register('user', function ($breadcrumbs) {
    generateBreadcrumbs($breadcrumbs, Lang::get('preylang.breadcrumb.user'), 'user.index');
});

//userSetting
Breadcrumbs::register('user.userSetting', function ($breadcrumbs) {
    generateBreadcrumbs($breadcrumbs, Lang::get('preylang.userSetting'), 'user.userSetting');
});

//setting
Breadcrumbs::register('usergroups', function ($breadcrumbs) {
    generateTwoLevelsBreadcrumbs($breadcrumbs, Lang::get('preylang.setting'), 'userGroups.index', Lang::get('preylang.userGroup'), 'userGroups.index');
});

Breadcrumbs::register('category', function ($breadcrumbs) {
    generateTwoLevelsBreadcrumbs($breadcrumbs, Lang::get('preylang.setting'), 'category.index', Lang::get('preylang.breadcrumb.category'), 'category.index');
});

Breadcrumbs::register('province', function ($breadcrumbs) {
    generateTwoLevelsBreadcrumbs($breadcrumbs, Lang::get('preylang.setting'), 'province.index', Lang::get('preylang.province'), 'province.index');
});

Breadcrumbs::register('proof', function ($breadcrumbs) {
    generateTwoLevelsBreadcrumbs($breadcrumbs, Lang::get('preylang.setting'), 'proof.index', Lang::get('preylang.setting.proof'), 'proof.index');
});

Breadcrumbs::register('reason', function ($breadcrumbs) {
    generateTwoLevelsBreadcrumbs($breadcrumbs, Lang::get('preylang.setting'), 'reason.index', Lang::get('preylang.setting.reason'), 'reason.index');
});

Breadcrumbs::register('offender', function ($breadcrumbs) {
    generateTwoLevelsBreadcrumbs($breadcrumbs, Lang::get('preylang.setting'), 'offender.index', Lang::get('preylang.setting.offender'), 'offender.index');
});

Breadcrumbs::register('designation', function ($breadcrumbs) {
    generateTwoLevelsBreadcrumbs($breadcrumbs, Lang::get('preylang.setting'), 'designation.index', Lang::get('preylang.setting.designation'), 'designation.index');
});

Breadcrumbs::register('threatening', function ($breadcrumbs) {
    generateTwoLevelsBreadcrumbs($breadcrumbs, Lang::get('preylang.setting'), 'threatening.index', Lang::get('preylang.setting.threatening'), 'threatening.index');
});

Breadcrumbs::register('victimType', function ($breadcrumbs) {
    generateTwoLevelsBreadcrumbs($breadcrumbs, Lang::get('preylang.setting'), 'victimType.index', Lang::get('preylang.setting.victimType'), 'victimType.index');
});

//villager
Breadcrumbs::register('villager', function ($breadcrumbs) {
    generateBreadcrumbs($breadcrumbs, Lang::get('preylang.sidebar.villager'), 'villager.index');
});

//activitiy
Breadcrumbs::register('activity', function ($breadcrumbs) {
    generateBreadcrumbs($breadcrumbs, Lang::get('preylang.breadcrumb.activity'), 'activity.index');
});

//impact
Breadcrumbs::register('impact', function ($breadcrumbs) {
    generateBreadcrumbs($breadcrumbs, Lang::get('preylang.breadcrumb.impact'), 'impact.index');
});

//map
Breadcrumbs::register('map', function ($breadcrumbs) {
    generateBreadcrumbs($breadcrumbs, Lang::get('preylang.breadcrumb.map'), 'map');
});

//raw impact
Breadcrumbs::register('rawImpact', function ($breadcrumbs) {
    generateBreadcrumbs($breadcrumbs, Lang::get('preylang.breadcrumb.rawImpact'), 'rawImpact.index');
});

if (! function_exists('generateBreadcrumbs')) {
    function generateBreadcrumbs($breadcrumbs, $current, $currentLink)
    {
        $breadcrumbs->push(Lang::get('preylang.breadcrumb.home'), route('dashboard'));
        $breadcrumbs->push(Lang::get($current), route($currentLink));
    }
}

if (! function_exists('generateTwoLevelsBreadcrumbs')) {
    function generateTwoLevelsBreadcrumbs($breadcrumbs, $parent, $parentLink, $current, $currentLink)
    {
        $breadcrumbs->push(Lang::get('preylang.breadcrumb.home'), route('dashboard'));
        $breadcrumbs->push(Lang::get($parent), route($parentLink));
        $breadcrumbs->push(Lang::get($current), route($currentLink));
    }
}
