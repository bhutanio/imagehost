@extends('layouts.base')

@section('header_css')
    <meta property="og:url" content="{{ url()->current() }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="{{ ($image->image_title ? $image->image_title : env('SITE_NAME')) }}"/>
    <meta property="og:description" content="{{ ($image->image_description ? $image->image_description : env('SITE_NAME')) }}"/>
    <meta property="og:image" content="{{ asset_cdn('i/'.$image->hash.'.'.$image->image_extension) }}"/>
@endsection

@section('page_title')
@endsection

@section('content')
    <section class="container">
        <div class="block">
            <div class="block-image pull-right">
                <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#embedModal">Embed</button>
            </div>

            <div class="block-image">
                <a href="{{ asset_cdn('i/'.$image->hash.'.'.$image->image_extension) }}" title=""><img src="{{ asset_cdn('i/'.$image->hash.'.'.$image->image_extension) }}" alt=""></a>
            </div>
        </div>
    </section>

    @include('blocks.embed')
@endsection
