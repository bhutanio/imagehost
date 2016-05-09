@extends('errors.error')

@section('page_title')
@endsection

@section('error_title')
    <i class="fa fa-cog fa-spin text-danger"></i> Error 500: Internal Server Error!
@endsection

@section('error_message')
    <p class="text-center">Our server encountered an internal error. <br>Error has been logged and reported to the System Administrator.</p>
@endsection
