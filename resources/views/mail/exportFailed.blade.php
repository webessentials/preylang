@extends('layouts.mail')

@section('message')
    @if ($fullName != ' ')
        <h1>Hello {{ $fullName }},</h1>
    @else
        <h1>Hello,</h1>
    @endif

    <p>
        Unfortunately your requested export of impacts has <strong>failed</strong>.<br/>
        Please try again later.
    </p>

    <p>
        Your Prey Lang Database Application
    </p>
@endsection
