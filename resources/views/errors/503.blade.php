@extends('errors.error')

@section('page_title')
@endsection

@section('content')
    <div class="jumbotron shadowed">
        <div class="container text-center">
            <h1 class="mt-5"><i class="fa fa-cogs text-warning"></i> Temporarily down for maintenance!</h1>
            <div class="separator"></div>
            <p>The hamsters powering our server are taking a break. <br>They should be back in couple of minutes.</p>
            <p>
                <a href="https://forums.{{ parse_url(env('APP_URL'), PHP_URL_HOST) }}" class="btn btn-lg btn-primary" title="Go to Forums"><i class="fa fa-comments-o"></i> Go to the Forums</a>
            </p>
        </div>
    </div>
    <div>
        <h2 class="text-bold">IRC Chat</h2>
        <iframe src="https://qchat.rizon.net/?channels=avistaz&uio=MTY9dHJ1ZSY5PXRydWUa8" width="100%" height="400" class="shadowed" style="border: none;"></iframe>
    </div>
@endsection
