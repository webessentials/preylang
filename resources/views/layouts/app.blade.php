<!DOCTYPE html>
<html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js" type="text/javascript"></script>
    @include('partials.metaTag')
    @include('partials.favicon')
    @include('partials.includeCss')
    @yield('specificJs')
    @include('partials.includeJs')
    <title>@yield('pageTitle') | {{ Lang::get('preylang.application.site.title') }}</title>
</head>
<body>
<div class="main-wrapper">
    <div class="app header-fixed" id="app">
        <!--header -->
        @include('partials.headers.header')
        <!--sidebar -->
        @include('partials.sidebar.sidebar')

        <div class="mobile-menu-handle"></div>

        <article class="content">
            @yield('content')
        </article>

        @include('partials.footers.footer')
    </div>
</div>
</body>
</html>
