@extends('admin-theme::layouts.admin')

@section('title', __('users::users.edit'))

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('core-localization::web.close') }}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('core-localization::web.close') }}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $errors->first() }}
        <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('core-localization::web.close') }}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@php
    $avatarPreviewUrl = old('avatar_preview_url', $user->avatar_url);
@endphp

<div class="card">
    <div class="card-header">
        <h5>{{ __('users::users.edit') }}</h5>
    </div>

    <div class="card-block">
        <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group form-primary">
                <label>{{ __('users::users.name') }}</label>
                <input
                    name="name"
                    class="form-control"
                    value="{{ old('name', $user->name) }}"
                    required
                >
                <span class="form-bar"></span>
                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="form-group form-primary">
                <label>{{ __('users::users.email') }}</label>
                <input
                    name="email"
                    type="email"
                    class="form-control"
                    value="{{ old('email', $user->email) }}"
                    required
                >
                <span class="form-bar"></span>
                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="form-group form-primary">
                <label>{{ __('users::users.password') }}</label>
                <input
                    name="password"
                    type="password"
                    class="form-control"
                >
                <span class="form-bar"></span>
                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="form-group form-primary">
                <label>{{ __('users::users.password_confirmation') }}</label>
                <input
                    name="password_confirmation"
                    type="password"
                    class="form-control"
                >
                <span class="form-bar"></span>
                @error('password_confirmation') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="d-block">{{ __('users::users.public.avatar') }}</label>

                <div class="mb-3">
                    <img
                        id="admin_avatar_preview"
                        src="{{ $avatarPreviewUrl }}"
                        alt="{{ $user->name }}"
                        style="width: 90px; height: 90px; object-fit: cover; border-radius: 50%;"
                    >
                </div>

                <input
                    type="file"
                    name="avatar"
                    class="form-control mb-2"
                    accept=".jpg,.jpeg,.png,.webp,.gif"
                >
                @error('avatar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                <input
                    type="hidden"
                    name="avatar_media_id"
                    id="admin_avatar_media_id"
                    value="{{ old('avatar_media_id', $user->avatar_media_id) }}"
                >

                <input
                    type="hidden"
                    name="avatar_preview_url"
                    id="admin_avatar_preview_url"
                    value="{{ $avatarPreviewUrl }}"
                >

                <div class="mt-2">
                    <button
                        type="button"
                        class="btn btn-sm btn-secondary waves-effect waves-light"
                        onclick="openMediaPicker('admin_avatar_media_id')"
                    >
                        {{ __('users::users.media.choose_from_library') }}
                    </button>
                </div>

                @error('avatar_media_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="form-check mb-3">
                <input
                    type="checkbox"
                    name="is_active"
                    class="form-check-input"
                    value="1"
                    {{ (bool) old('is_active', $user->is_active) ? 'checked' : '' }}
                >
                <label class="form-check-label">{{ __('users::users.is_active') }}</label>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">{{ __('users::users.roles') }}</label>

                @php
                    $checkedRoleSlugs = old('role_slugs', $selectedRoleSlugs ?? []);
                @endphp

                @foreach($roles as $role)
                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="role_slugs[]"
                            value="{{ $role->slug }}"
                            class="form-check-input"
                            id="role_slug_{{ $role->slug }}"
                            {{ in_array($role->slug, $checkedRoleSlugs, true) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="role_slug_{{ $role->slug }}">{{ $role->name }}</label>
                    </div>
                @endforeach

                @error('role_slugs') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                @error('role_slugs.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mt-3">
                <button class="btn btn-primary waves-effect waves-light">{{ __('users::users.save') }}</button>
                <a href="{{ route('users.show', $user) }}" class="btn btn-secondary waves-effect waves-light">
                    {{ __('users::users.show') }}
                </a>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                    {{ __('users::users.back') }}
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function openMediaPicker(targetId) {
        const pickerUrl = "{{ route('media.index') }}" + "?picker=1&target=" + encodeURIComponent(targetId);
        window.open(pickerUrl, "mediaPicker", "width=1200,height=800,resizable=yes,scrollbars=yes");
    }

    window.setMediaPickerValue = function (targetId, mediaId, mediaUrl) {
        const input = document.getElementById(targetId);
        const preview = document.getElementById('admin_avatar_preview');
        const previewUrl = document.getElementById('admin_avatar_preview_url');

        if (input) {
            input.value = mediaId;
        }

        if (preview && mediaUrl) {
            preview.src = mediaUrl;
        }

        if (previewUrl && mediaUrl) {
            previewUrl.value = mediaUrl;
        }
    };
</script>

@endsection