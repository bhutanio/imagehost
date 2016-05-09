@extends('errors.error')

@section('page_title')
@endsection

@section('error_title')
    <i class="fa fa-exclamation-circle text-warning"></i> Error 404: {{ $exception->getMessage() ?: 'Page not found!' }}
@endsection
