<div class="form-group form-primary">
    <label for="name" class="form-label">{{ __('permissions::permissions.fields.name') }}</label>
    <input
        type="text"
        name="name"
        id="name"
        value="{{ old('name', $permission?->name) }}"
        required
        class="form-control"
    >
    <span class="form-bar"></span>
    <small class="text-muted">{{ __('permissions::permissions.help.name') }}</small>
    @error('name')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">{{ __('permissions::permissions.translations_title') }}</h6>
    </div>

    <div class="card-block">
        @foreach($languages as $language)
            <div class="border rounded p-3 mb-3">
                <h6 class="mb-3">{{ $language->nativeName ?: $language->name }} ({{ $language->code }})</h6>

                <div class="form-group form-primary">
                    <label for="translations_{{ $language->code }}_label" class="form-label">{{ __('permissions::permissions.fields.localized_label') }}</label>
                    <input
                        type="text"
                        name="translations[{{ $language->code }}][label]"
                        id="translations_{{ $language->code }}_label"
                        value="{{ old('translations.' . $language->code . '.label', $translationInputs[$language->code]['label'] ?? '') }}"
                        class="form-control"
                    >
                    <span class="form-bar"></span>
                    @error('translations.' . $language->code . '.label')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group form-primary mb-0">
                    <label for="translations_{{ $language->code }}_description" class="form-label">{{ __('permissions::permissions.fields.localized_description') }}</label>
                    <textarea
                        name="translations[{{ $language->code }}][description]"
                        id="translations_{{ $language->code }}_description"
                        rows="3"
                        class="form-control"
                    >{{ old('translations.' . $language->code . '.description', $translationInputs[$language->code]['description'] ?? '') }}</textarea>
                    <span class="form-bar"></span>
                    @error('translations.' . $language->code . '.description')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @endforeach

        @error('translations')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-group form-primary">
    <label for="label" class="form-label">{{ __('permissions::permissions.fields.system_label') }}</label>
    <input
        type="text"
        name="label"
        id="label"
        value="{{ old('label', $permission?->label) }}"
        class="form-control"
    >
    <span class="form-bar"></span>
    <small class="text-muted">{{ __('permissions::permissions.help.label') }}</small>
    @error('label')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="form-group form-primary">
    <label for="description" class="form-label">{{ __('permissions::permissions.fields.system_description') }}</label>
    <textarea
        name="description"
        id="description"
        rows="4"
        class="form-control"
    >{{ old('description', $permission?->description) }}</textarea>
    <span class="form-bar"></span>
    <small class="text-muted">{{ __('permissions::permissions.help.description') }}</small>
    @error('description')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label class="form-label d-block">{{ __('permissions::permissions.fields.roles') }}</label>

    <div class="border rounded p-3">
        @foreach($roles as $role)
            <div class="form-check mb-2">
                <input
                    type="checkbox"
                    name="role_ids[]"
                    value="{{ $role->id }}"
                    id="role_{{ $role->id }}"
                    class="form-check-input"
                    @checked(in_array($role->id, array_map('intval', old('role_ids', $selectedRoleIds ?? [])), true))
                >
                <label for="role_{{ $role->id }}" class="form-check-label">
                    {{ $role->name }} @if(!empty($role->slug)) ({{ $role->slug }}) @endif
                </label>
            </div>
        @endforeach
    </div>

    @error('role_ids')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary waves-effect waves-light">{{ $submitLabel }}</button>
    <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">{{ __('permissions::permissions.actions.back') }}</a>
</div>