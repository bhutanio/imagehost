@extends('errors.error')

@section('page_title')
@endsection

@section('error_title')
    <i class="fa fa-times text-danger"></i> Error 402: {{ $exception->getMessage() ?: 'Limit Exceeded!' }}
@endsection

@section('error_message')
    <p class="text-center">You have exceeded your limits.</p>
@endsection
