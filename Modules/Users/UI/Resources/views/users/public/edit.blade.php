@extends('public-theme::layouts.public')

@section('title', __('users::users.public.edit_profile'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('core-localization::web.close') }}"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">{{ __('users::users.public.edit_profile') }}</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('users::users.name') }}</label>
                            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">{{ __('users::users.public.slug') }}</label>
                            <input id="slug" name="slug" type="text" class="form-control" value="{{ old('slug', $user->slug) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('users::users.email') }}</label>
                            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">{{ __('users::users.public.bio') }}</label>
                            <textarea id="bio" name="bio" rows="5" class="form-control">{{ old('bio', $user->bio) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">{{ __('users::users.public.avatar') }}</label>

                            <div class="mb-3">
                                <img
                                    src="{{ $user->avatar_url }}"
                                    alt="{{ $user->name }}"
                                    style="width: 96px; height: 96px; object-fit: cover; border-radius: 50%;"
                                >
                            </div>

                            <input id="avatar" name="avatar" type="file" class="form-control mb-2" accept=".jpg,.jpeg,.png,.webp,.gif">

                            @error('avatar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('users::users.public.new_password') }}</label>
                            <input id="password" name="password" type="password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('users::users.public.password_confirmation') }}</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('users::users.public.current_password_required') }}</label>
                            <input id="current_password" name="current_password" type="password" class="form-control" required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ __('core-localization::web.save') }}
                            </button>

                            <a href="{{ route('profile.me') }}" class="btn btn-outline-secondary">
                                {{ __('core-localization::web.back') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection