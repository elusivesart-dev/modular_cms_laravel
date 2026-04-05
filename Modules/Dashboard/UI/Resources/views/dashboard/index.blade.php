@extends('admin-theme::layouts.admin')

@section('title', __('core-localization::web.dashboard'))

@section('content')

@php
    $defaultAvatar = admin_theme_asset('images/avatar-4.jpg');
@endphp

<div class="row">
    <div class="col-xl-6 col-md-12">
        <div class="card mat-stat-card">
            <div class="card-block">
                <div class="row align-items-center b-b-default">
                    <div class="col-sm-6 b-r-default p-b-20 p-t-20">
                        <a href="javascript:void(0)" class="text-decoration-none text-reset d-block">
                            <div class="row align-items-center text-center">
                                <div class="col-4 p-r-0">
                                    <i class="ti-layers text-c-purple f-24"></i>
                                </div>
                                <div class="col-8 p-l-0">
                                    <h5>{{ $modulesCount }}</h5>
                                    <p class="text-muted m-b-0">{{ __('core-localization::web.modules') }}</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-6 p-b-20 p-t-20">
                        <a href="{{ route('users.index') }}" class="text-decoration-none text-reset d-block">
                            <div class="row align-items-center text-center">
                                <div class="col-4 p-r-0">
                                    <i class="ti-user text-c-green f-24"></i>
                                </div>
                                <div class="col-8 p-l-0">
                                    <h5>{{ $usersCount }}</h5>
                                    <p class="text-muted m-b-0">{{ __('core-localization::web.users') }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-sm-6 p-b-20 p-t-20 b-r-default">
                        <a href="{{ route('themes.index') }}" class="text-decoration-none text-reset d-block">
                            <div class="row align-items-center text-center">
                                <div class="col-4 p-r-0">
                                    <i class="ti-palette text-c-red f-24"></i>
                                </div>
                                <div class="col-8 p-l-0">
                                    <h5>{{ $themesCount }}</h5>
                                    <p class="text-muted m-b-0">{{ __('core-localization::web.themes') }}</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-6 p-b-20 p-t-20">
                        <a href="{{ route('localization.languages.index') }}" class="text-decoration-none text-reset d-block">
                            <div class="row align-items-center text-center">
                                <div class="col-4 p-r-0">
                                    <i class="ti-world text-c-blue f-24"></i>
                                </div>
                                <div class="col-8 p-l-0">
                                    <h5>{{ $languagesCount }}</h5>
                                    <p class="text-muted m-b-0">{{ __('core-localization::web.languages') }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-md-12">
        <div class="card table-card">
            <div class="card-header">
                <h5>{{ __('core-localization::web.recent_actions') }}</h5>
                <div class="card-header-right">
                    <a href="{{ route('audit.index') }}" class="btn btn-sm btn-primary waves-effect waves-light">
                        {{ __('core-localization::web.view_all') }}
                    </a>
                </div>
            </div>

            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-hover m-b-0 without-header">
                        <tbody>
                        @forelse($recentActions as $action)
                            @php
                                $auditModel = $action['model'] ?? null;
                                $actor = $auditModel?->actor;

                                $actorName = $action['author'] ?? __('audit::audit.system');
                                $actorAvatar = $actor?->avatar_url ?? $defaultAvatar;

                                $actionText = $action['action'] ?? __('audit::audit.unknown');
                                $actionUser = $action['user'] ?? '—';
                                $actionTime = $action['created_at'] ?? '—';
                            @endphp

                            <tr>
                                <td>
                                    <a href="{{ route('audit.index') }}" class="text-decoration-none text-reset d-block">
                                        <div class="d-inline-block align-middle">
                                            <img
                                                src="{{ $actorAvatar }}"
                                                alt="{{ $actorName }}"
                                                class="img-radius img-40 align-top m-r-15"
                                                style="width: 40px; height: 40px; object-fit: cover;"
                                            >

                                            <div class="d-inline-block">
                                                <h6 class="m-b-5">{{ $actorName }}</h6>
                                                <p class="text-muted m-b-5">{{ $actionText }}</p>

                                                <small class="text-muted">
                                                    {{ __('audit::audit.user') }}: {{ $actionUser }} · {{ $actionTime }}
                                                </small>

                                                @if(!empty($action['details']) && is_array($action['details']))
                                                    <div class="m-t-5">
                                                        @foreach($action['details'] as $detail)
                                                            <span class="badge badge-light mr-1 mb-1">{{ $detail }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-muted p-t-30 p-b-30">
                                    {{ __('audit::audit.no_records') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($recentActions->hasPages())
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $recentActions->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection