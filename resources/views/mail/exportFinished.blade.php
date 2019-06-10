@extends('layouts.mail')

@section('message')
    @if ($fullName != '')
        <h1>Hello {{ $fullName }},</h1>
        <p>
            Your requested export of impacts has finished successfully.<br/>
        </p>
    @else
        <h1>Hello,</h1>
        <p>
            Someone has requested us to deliver an export of recorded impacts to you.
        </p>
    @endif
    <p>
        You can download the file <a href="{{ route('files.redirectToDownload', $filePath) }}" target="_blank" style="color: rgb(133, 206, 54)"><strong>here</strong></a>.
    </p>

    <p>
        Insightful Analysis,<br/>
        Your Prey Lang Database Application
    </p>
@endsection
