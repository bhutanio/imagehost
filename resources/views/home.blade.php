@extends('layouts.app')

@section('content')
    <div class="block">
        <h1 class="text-center">Upload and share images securely with {{ env('SITE_NAME') }}!</h1>
        <p class="lead text-center">Select the images and click upload.</p>

        {!! Form::open(['files'=>true, 'url'=>url('image/create'), 'id'=>'form_upload', 'class' => 'form-horizontal', 'role'=>'form']) !!}

        <div class="form-group">
            <div class="col-sm-12">
                <div id="images_fileuploader"></div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                {!! Form::text('title', null, ['class' => 'form-control', 'placeholder'=>'Image/Album Title']) !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                {!! Form::textarea('description', null, ['class' => 'form-control', 'rows'=>'3', 'placeholder'=>'Image/Album Description']) !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-1 col-md-1 col-sm-2"><label for="adult" class="control-label">Adult: </label></div>
            <div class="col-lg-1 col-md-1 col-sm-2">
                <label class="control-label">{!! Form::radio('adult', 1, (old('adult') ? old('adult') : false), []) !!} Yes </label></div>
            <div class="col-lg-1 col-md-1 col-sm-2">
                <label class="control-label">{!! Form::radio('adult', 0, (old('adult') ? old('adult') : true), []) !!} No </label></div>
            <div class="col-sm-12"><p class="help-block">Is this image Safe for Work?</p></div>
        </div>
        <div class="form-group">
            <div class="col-lg-1 col-md-1 col-sm-2"><label for="private" class="control-label">Private: </label></div>
            <div class="col-lg-1 col-md-1 col-sm-2">
                <label class="control-label">{!! Form::radio('private', 1, (old('private') ? old('private') : true), []) !!} Yes </label>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-2">
                <label class="control-label">{!! Form::radio('private', 0, (old('private') ? old('private') : false), []) !!} No </label>
            </div>
            <div class="col-sm-12"><p class="help-block">Should this image be publicly accessible?</p></div>
        </div>

        <div class="form-group">
            <div class="col-lg-1 col-md-1 col-sm-2"><label for="expire" class="control-label">Expire: </label></div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                {!! Form::select('expire', [
                0 => 'Never',
                10 => '10 Minutes',
                60 => '1 Hour',
                1440 => '1 Day',
                10080 => '1 Week',
                43800 => '1 Month',
                ], null, ['class'=>'form-control']) !!}
            </div>
            <div class="col-sm-12">
                <p class="help-block">This image will be deleted after?</p>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                {!! Form::submit('Upload', ['id'=>'btn_upload', 'class'=>'btn btn-info']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </div>
    @include('blocks.fineuploader')
@endsection
