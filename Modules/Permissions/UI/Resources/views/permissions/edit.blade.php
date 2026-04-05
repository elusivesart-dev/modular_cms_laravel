@extends('admin-theme::layouts.admin')

@section('title', __('permissions::permissions.edit_title'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ __('permissions::permissions.edit_title') }}</h5>
        </div>

        <div class="card-block">
            <form action="{{ route('permissions.update', $permission) }}" method="POST">
                @csrf
                @method('PUT')

                @include('permissions::permissions.partials.form', [
                    'permission' => $permission,
                    'submitLabel' => __('permissions::permissions.actions.update'),
                ])
            </form>
        </div>
    </div>
@endsection