@extends('layouts.base')

@section('header_css')
    <meta property="og:url" content="{{ url()->current() }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="{{ meta()->pageTitle() }}"/>
    <meta property="og:description" content="{{ ($album->album_description ? $album->album_description : env('SITE_NAME')) }}"/>
    <meta property="og:image" content="{{ asset_cdn('i/'.$album->images->first()->hash.'.'.$album->images->first()->image_extension) }}"/>
@endsection

@section('page_title')
@endsection

@section('content')
    <section class="container">
        <h1 class="page-title">{{ meta()->pageTitle() }}</h1>
        @if(!empty($album->album_description))
            <p>{{ $album->album_description }}</p>
        @endif
        <div class="block">
            <div class="block-image pull-right">
                <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#embedModal">Embed</button>
            </div>
            <div class="clearfix"></div>

            @foreach($album->images as $image)
                <div class="block-image">
                    @if(!empty($image->image_title))
                        <h3>{{ $image->image_title }}</h3>
                    @endif
                    <a href="{{ url('i/'.$image->hash) }}" title="{{ ($image->image_title ? $image->image_title : 'Image '.$image->hash) }}"><img src="{{ asset_cdn('i/'.$image->hash.'.'.$image->image_extension) }}" alt=""></a>
                    @if(!empty($image->image_description))
                        <p>{{ $image->image_description }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    @include('blocks.embed', ['image' => $album->images])
@endsection
