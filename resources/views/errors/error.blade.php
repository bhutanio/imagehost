<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ meta()->metaTitle() }}</title>
    @if(meta()->description())
        <meta name="description" content="{{ meta()->description() }}">
    @endif
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="_base_url" content="{{ url('/') }}">
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,500,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Raleway:300,500,700' rel='stylesheet' type='text/css'>

    <link href="{{ asset('css/style.css?v='.asset_version()) }}" rel="stylesheet">
    @yield('header_css')

<!--[if lte IE 8]>
    <script src="{{ asset('js/html5shiv.respond.min.js') }}"></script><![endif]-->
</head>

<body>
<section class="container content" id="content-area">
    @section('page_title')
        <h1 class="title">{{ meta()->pageTitle() }}</h1>
    @show
    @section('content')
        <div class="jumbotron shadowed">
            <div class="container">
                <h1 class="mt-5 text-center">
                    @section('error_title')
                        {{ meta()->pageTitle() }}: Page not found!
                    @show
                </h1>
                <div class="separator"></div>
                @section('error_message')
                    <p>The requested URL was not found on this server. Make sure that the Web site address displayed in the address bar of your browser is spelled and formatted correctly.</p>
                @show

                <p class="text-center">
                    @section('button_back')
                        <a href="javascript:history.back()" class="btn btn-lg btn-info" title="Back to where ever you came from"><i class="fa fa-backward"></i> Go Back</a>
                    @show
                    @section('button_home')
                        <a href="{{ url('/') }}" class="btn btn-lg btn-primary" title="Go to Home Page"><i class="fa fa-home"></i> Go to Home Page</a>
                    @show
                </p>
            </div>
        </div>
    @show
</section>
@yield('footer_js')
</body>
</html>