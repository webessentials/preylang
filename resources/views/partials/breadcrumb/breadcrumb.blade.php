<div class="line">
    <div class="row">
        <div class="col-xl-8 col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @foreach(Breadcrumbs::generate($breadcrumb) as $breadcrumb)
                    @if($breadcrumb->url)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
                    @endif
                    @endforeach
                </ol>
            </nav>
        </div>

        <div class="col-xl-4 col-md-4">
            @include('partials.headers.nav.userGroup.userGroup')
        </div>
    </div>
</div>
