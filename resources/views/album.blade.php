@extends('layouts.base')

@section('header_css')
    <meta property="og:url" content="{{ url()->current() }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="{{ ($album->album_title ? $album->album_title : env('SITE_NAME')) }}"/>
    <meta property="og:description" content="{{ ($album->album_description ? $album->album_description : env('SITE_NAME')) }}"/>
    <meta property="og:image" content="{{ asset_cdn('i/'.$album->images->first()->hash.'.'.$album->images->first()->image_extension) }}"/>
@endsection

@section('page_title')
@endsection

@section('content')
    <section class="container">
        <div class="block">
            <div class="block-image pull-right">
                <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#embedModal">Embed</button>
            </div>

            @foreach($album->images as $image)
                <div class="block-image">
                    <a href="{{ url('i/'.$image->hash) }}" title=""><img src="{{ asset_cdn('i/'.$image->hash.'.'.$image->image_extension) }}" alt=""></a>
                </div>
            @endforeach
        </div>
    </section>

    @include('blocks.embed', ['image' => $album->images])
@endsection
