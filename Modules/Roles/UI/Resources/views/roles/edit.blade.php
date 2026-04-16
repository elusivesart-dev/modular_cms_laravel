@extends('admin-theme::layouts.admin')

@section('title', __('roles::roles.edit'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ __('roles::roles.edit') }}</h5>
        </div>

        <div class="card-block">
            <form method="POST" action="{{ route('roles.update', $role) }}">
                @csrf
                @method('PUT')

                <div class="form-group form-primary">
                    <label for="slug" class="form-label">{{ __('roles::roles.slug') }}</label>
                    <input id="slug" name="slug" type="text" class="form-control" value="{{ old('slug', $role->slug) }}" required>
                    <span class="form-bar"></span>
                    <small class="form-text text-muted">{{ __('roles::roles.slug_help') }}</small>
                    @error('slug') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="form-check mb-4">
                    <input id="is_system" name="is_system" type="checkbox" class="form-check-input" value="1" {{ old('is_system', $role->is_system) ? 'checked' : '' }}>
                    <label for="is_system" class="form-check-label">{{ __('roles::roles.is_system') }}</label>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('roles::roles.translations_title') }}</h6>
                    </div>

                    <div class="card-block">
                        @foreach($languages as $language)
                            <div class="border rounded p-3 mb-3">
                                <h6 class="mb-3">{{ $language->nativeName ?: $language->name }} ({{ $language->code }})</h6>

                                <div class="form-group form-primary">
                                    <label for="translations_{{ $language->code }}_name" class="form-label">{{ __('roles::roles.localized_name') }}</label>
                                    <input
                                        id="translations_{{ $language->code }}_name"
                                        name="translations[{{ $language->code }}][name]"
                                        type="text"
                                        class="form-control"
                                        value="{{ old('translations.' . $language->code . '.name', $translationInputs[$language->code]['name'] ?? '') }}"
                                    >
                                    <span class="form-bar"></span>
                                    @error('translations.' . $language->code . '.name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group form-primary mb-0">
                                    <label for="translations_{{ $language->code }}_description" class="form-label">{{ __('roles::roles.localized_description') }}</label>
                                    <textarea
                                        id="translations_{{ $language->code }}_description"
                                        name="translations[{{ $language->code }}][description]"
                                        class="form-control"
                                        rows="3"
                                    >{{ old('translations.' . $language->code . '.description', $translationInputs[$language->code]['description'] ?? '') }}</textarea>
                                    <span class="form-bar"></span>
                                    @error('translations.' . $language->code . '.description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        @endforeach

                        @error('translations') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label d-block">{{ __('roles::roles.permissions') }}</label>

                    <div class="border rounded p-3">
                        @forelse($permissions as $permission)
                            @php
                                $permissionLabel = $permission->name;

                                if (isset($permission->display_label) && is_string($permission->display_label) && $permission->display_label !== '') {
                                    $permissionLabel = $permission->display_label;
                                } elseif (!empty($permission->label) && is_string($permission->label) && str_contains($permission->label, '::')) {
                                    $permissionLabel = __($permission->label);
                                } elseif (!empty($permission->label)) {
                                    $permissionLabel = (string) $permission->label;
                                }
                            @endphp

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
                                    {{ $permission->name }} — {{ $permissionLabel }}
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
                    <button type="submit" class="btn btn-primary waves-effect waves-light">{{ __('roles::roles.update') }}</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">{{ __('roles::roles.back') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection