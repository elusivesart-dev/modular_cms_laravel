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

<div class="form-group form-primary">
    <label for="label" class="form-label">{{ __('permissions::permissions.fields.label') }}</label>
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
    <label for="description" class="form-label">{{ __('permissions::permissions.fields.description') }}</label>
    <textarea
        name="description"
        id="description"
        rows="4"
        class="form-control"
    >{{ old('description', $permission?->description) }}</textarea>
    <span class="form-bar"></span>
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