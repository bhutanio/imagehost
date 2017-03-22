@extends('layouts.app')

@section('content')
    <h1 class="page-title">{{ meta()->pageTitle() }}</h1>
    <div class="block row">
        @if($images->count())
            <div class="row">
                @foreach($images as $image)
                    <div class="col-lg-2 col-md-3 col-sm-4" style="padding-bottom: 4px;">
                        <a href="{{ url('i/'.$image->hash) }}" title=""><img src="{{ asset_cdn('t/'.$image->hash.'.'.$image->image_extension) }}" alt=""></a>
                    </div>
                @endforeach
            </div>
            <div class="clearfix"></div>
        @endif
    </div>
    <div class="pull-left" style="">{!! $images->render() !!}</div>
@endsection