@extends('layouts.app')

@section('content')
    <h1 class="page-title">{{ meta()->pageTitle() }}</h1>
    <div class="block row">
        @if($images->count())
            @foreach($images as $image)
                <div class="col-sm-3 block-image">
                    <p>
                        <strong><a href="{{ url('i/'.$image->hash) }}" title="{{ ($image->image_title ? $image->image_title : 'Image '.$image->hash) }}">{{ ($image->image_title ? $image->image_title : 'Image '.$image->hash) }}</a></strong>
                    </p>
                    <a href="{{ url('i/'.$image->hash) }}" title=""><img src="{{ asset_cdn('t/'.$image->hash.'.'.$image->image_extension) }}" alt=""></a>
                    <p>&nbsp;</p>
                </div>
            @endforeach
            <div class="clearfix"></div>
        @endif
    </div>
    <div class="pull-left" style="">{!! $images->render() !!}</div>
@endsection