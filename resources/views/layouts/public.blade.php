<!DOCTYPE html>
<html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js" type="text/javascript"></script>
    @include('partials.metaTag')
    @include('partials.favicon')
    @include('partials.includePublicCss')
    @include('partials.includePublicJs')
    <title>@yield('pageTitle')</title>
</head>
<body>
    <div class="content">
        @yield('content')
    </div>
</body>
</html>
