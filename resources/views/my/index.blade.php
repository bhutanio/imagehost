@extends('layouts.app')

@section('content')
    <section class="container">
        <h1 class="page-title">{{ meta()->pageTitle() }}</h1>
        <div class="block">
            <h2><a href="{{ url($url.'/albums') }}" title="My Albums">Albums : {{ $albums }}</a></h2>
            <h2><a href="{{ url($url.'/images') }}" title="My Images">Images : {{ $images }}</a></h2>
        </div>
    </section>
@endsection