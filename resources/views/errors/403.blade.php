@extends('errors.layout')

@section('code', '403')
@section('title', __('errors.403.title'))
@section('message', __('errors.403.message'))

@section('extra_actions')
    <a class="btn ghost" href="{{ url('/contact') }}">{{ __('errors.actions.contact') }}</a>
@endsection
