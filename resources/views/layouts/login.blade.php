<!DOCTYPE html>
<html>
<head>
    @include('partials.metaTag')
    @include('partials.favicon')
    @include('partials.includeCss')
    @include('partials.includeJs')
    <title>@yield('pageTitle')</title>
</head>
<body>
    @yield('content')
</body>
</html>
