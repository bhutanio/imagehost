@extends('layouts.app')

@section('content')
    <h1 class="page-title">{{ meta()->pageTitle() }}</h1>
    <div class="block">
        @if($albums->count())
            @foreach($albums as $album)
                <div class="block-image">
                    <button type="button" data-album-id="{{ $album->id }}" class="btn_delete_album btn btn-sm btn-danger pull-right"><i class="fa fa-trash-o"></i></button>
                    <h3>
                        <a href="{{ url('a/'.$album->hash) }}" title="{{ ($album->album_title ? $album->album_title : 'Album '.$album->hash) }}">{{ ($album->album_title ? $album->album_title : 'Album '.$album->hash) }}</a>
                        <span class=""> ({{ $album->images->count() }} Images)</span>
                    </h3>
                    @foreach($album->images->slice(0,6) as $image)
                        <a href="{{ url('i/'.$image->hash) }}" title=""><img src="{{ asset_cdn('t/'.$image->hash.'.'.$image->image_extension) }}" alt=""></a>
                    @endforeach
                    <p>{{ $album->album_description or '' }}</p>
                </div>
            @endforeach
        @endif
    </div>
    <div class="pull-left" style="">{!! $albums->render() !!}</div>
@endsection