@extends('errors::layout')

@section('title', 'Unauthorized Access')
@section('code', '401')
@section('icon', 'bi-lock')

@section('message')
    You need to be authenticated to access this page. Please log in to continue.
@endsection

@section('actions')
    <a href="{{ route('login') }}" class="btn-error btn-error-primary">
        <i class="bi bi-box-arrow-in-right"></i>
        <span>Login</span>
    </a>
    <a href="{{ route('welcome') }}" class="btn-error btn-error-secondary">
        <i class="bi bi-house"></i>
        <span>Go to Home</span>
    </a>
@endsection
