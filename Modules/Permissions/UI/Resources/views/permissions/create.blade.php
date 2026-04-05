@extends('admin-theme::layouts.admin')

@section('title', __('permissions::permissions.create_title'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ __('permissions::permissions.create_title') }}</h5>
        </div>

        <div class="card-block">
            <form action="{{ route('permissions.store') }}" method="POST">
                @csrf

                @include('permissions::permissions.partials.form', [
                    'permission' => null,
                    'submitLabel' => __('permissions::permissions.actions.save'),
                ])
            </form>
        </div>
    </div>
@endsection