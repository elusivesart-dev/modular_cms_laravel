@extends('admin-theme::layouts.admin')

@section('title', __('roles::roles.create'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ __('roles::roles.create') }}</h5>
        </div>

        <div class="card-block">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf

                <div class="form-group form-primary">
                    <label for="name" class="form-label">{{ __('roles::roles.name') }}</label>
                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" required>
                    <span class="form-bar"></span>
                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="form-group form-primary">
                    <label for="slug" class="form-label">{{ __('roles::roles.slug') }}</label>
                    <input id="slug" name="slug" type="text" class="form-control" value="{{ old('slug') }}" required>
                    <span class="form-bar"></span>
                    @error('slug') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="form-group form-primary">
                    <label for="description" class="form-label">{{ __('roles::roles.description') }}</label>
                    <textarea id="description" name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                    <span class="form-bar"></span>
                    @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="form-check mb-4">
                    <input id="is_system" name="is_system" type="checkbox" class="form-check-input" value="1" {{ old('is_system') ? 'checked' : '' }}>
                    <label for="is_system" class="form-check-label">{{ __('roles::roles.is_system') }}</label>
                </div>

                <div class="form-group">
                    <label class="form-label d-block">{{ __('roles::roles.permissions') }}</label>

                    <div class="border rounded p-3">
                        @forelse($permissions as $permission)
                            <div class="form-check mb-2">
                                <input
                                    id="permission_{{ $permission->id }}"
                                    name="permission_ids[]"
                                    type="checkbox"
                                    class="form-check-input"
                                    value="{{ $permission->id }}"
                                    {{ in_array((int) $permission->id, array_map('intval', old('permission_ids', $selectedPermissionIds ?? [])), true) ? 'checked' : '' }}
                                >
                                <label for="permission_{{ $permission->id }}" class="form-check-label">
                                    {{ $permission->name }}@if($permission->label) — {{ $permission->label }}@endif
                                </label>
                            </div>
                        @empty
                            <div class="text-muted small">{{ __('roles::roles.no_permissions_found') }}</div>
                        @endforelse
                    </div>

                    @error('permission_ids') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    @error('permission_ids.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">{{ __('roles::roles.save') }}</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">{{ __('roles::roles.back') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection