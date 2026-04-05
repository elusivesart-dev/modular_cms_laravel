@extends('public-theme::layouts.public')

@section('title', $profileUser->name)

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('core-localization::web.close') }}"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('core-localization::web.close') }}"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $profileUser->name }}</h4>

                    @if($isOwnProfile)
                        <span class="badge text-bg-primary">
                            {{ __('users::users.public.my_profile') }}
                        </span>
                    @endif
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <img
                            src="{{ $profileUser->avatar_url }}"
                            alt="{{ $profileUser->name }}"
                            class="img-thumbnail"
                            style="max-width: 180px;"
                        >
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('users::users.name') }}:</strong>
                        <div>{{ $profileUser->name }}</div>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('users::users.public.slug') }}:</strong>
                        <div>{{ $profileUser->slug }}</div>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('users::users.public.status') }}:</strong>
                        <div>{{ $profileUser->is_active ? __('users::users.public.active') : __('users::users.public.inactive') }}</div>
                    </div>

                    @if(!empty($profileUser->bio))
                        <div class="mb-3">
                            <strong>{{ __('users::users.public.bio') }}:</strong>
                            <div>{{ $profileUser->bio }}</div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>{{ __('users::users.public.joined_at') }}:</strong>
                        <div>{{ optional($profileUser->created_at)?->format('Y-m-d H:i') ?? '—' }}</div>
                    </div>

                    @if($isOwnProfile)
                        <div class="mb-3">
                            <strong>{{ __('users::users.email') }}:</strong>
                            <div>{{ $profileUser->email }}</div>
                        </div>
                    @endif

                    <div class="mt-4 d-flex gap-2">
                        @if($isOwnProfile)
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                {{ __('users::users.public.edit_profile') }}
                            </a>
                        @endif

                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                            {{ __('core-localization::web.back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection