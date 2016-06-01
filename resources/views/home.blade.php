@extends('layouts.base')

@section('page_title')
@endsection

@section('content')
    <section class="container">
        <div class="block">
            <h1 class="page-title">Upload and share images securely with {{ env('SITE_NAME') }}!</h1>
            <p class="lead text-center">Select the images and click upload.</p>

            {!! Form::open(['files'=>true, 'url'=>url('image/create'), 'id'=>'form_upload', 'class' => 'form-horizontal', 'role'=>'form']) !!}

            <div class="form-group">
                <div class="col-sm-12">
                    <div id="images_fileuploader"></div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    {!! Form::submit('Upload', ['id'=>'btn_upload', 'class'=>'btn btn-info']) !!}
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </section>
    @include('blocks.fineuploader')
@endsection
