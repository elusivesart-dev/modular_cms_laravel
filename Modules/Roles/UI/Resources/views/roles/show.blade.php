@extends('admin-theme::layouts.admin')

@section('title', __('roles::roles.show'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ __('roles::roles.show') }}</h5>
        </div>

        <div class="card-block">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label d-block">{{ __('roles::roles.name') }}</label>
                        <div class="form-control-plaintext">{{ $role->name }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label d-block">{{ __('roles::roles.slug') }}</label>
                        <div class="form-control-plaintext">{{ $role->slug }}</div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label d-block">{{ __('roles::roles.description') }}</label>
                <div class="form-control-plaintext">
                    {{ $role->description ?: '—' }}
                </div>
            </div>

            <div class="form-group">
                <label class="form-label d-block">{{ __('roles::roles.is_system') }}</label>
                <div class="form-control-plaintext">
                    {{ $role->is_system ? __('roles::roles.yes') : __('roles::roles.no') }}
                </div>
            </div>

            <div class="form-group">
                <label class="form-label d-block">{{ __('roles::roles.permissions') }}</label>

                <div class="border rounded p-3">
                    @forelse($role->permissions as $permission)
                        <div class="mb-2">
                            {{ $permission->name }}@if($permission->label) — {{ __($permission->label) }}@endif
                        </div>
                    @empty
                        <div class="text-muted small">{{ __('roles::roles.no_permissions_found') }}</div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary waves-effect waves-light">
                    {{ __('roles::roles.edit') }}
                </a>
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                    {{ __('roles::roles.back') }}
                </a>
            </div>
        </div>
    </div>
@endsection