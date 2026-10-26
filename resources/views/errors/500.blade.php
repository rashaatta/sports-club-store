@extends('errors.layout')

@section('code', '500')
@section('title', __('errors.500.title'))
@section('message', __('errors.500.message'))

@section('extra_actions')
    <a class="btn ghost" href="{{ url('/contact') }}">{{ __('errors.actions.contact') }}</a>
@endsection
