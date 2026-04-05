<div class="form-group form-primary">
    <label>{{ __('settings::settings.group') }}</label>
    <input name="group" class="form-control" value="{{ old('group', $setting->group ?? '') }}" required>
    <span class="form-bar"></span>
</div>

<div class="form-group form-primary">
    <label>{{ __('settings::settings.key') }}</label>
    <input name="key" class="form-control" value="{{ old('key', $setting->key ?? '') }}" required>
    <span class="form-bar"></span>
</div>

<div class="form-group form-primary">
    <label>{{ __('settings::settings.value') }}</label>
    <input name="value" class="form-control" value="{{ old('value', $setting->value ?? '') }}">
    <span class="form-bar"></span>
</div>

<div class="form-group form-primary">
    <label>{{ __('settings::settings.type') }}</label>
    <select name="type" class="form-control">
        @foreach(['string','text','integer','boolean','json'] as $type)
            <option value="{{ $type }}" @selected(old('type', $setting->type ?? 'string') === $type)>
                {{ $type }}
            </option>
        @endforeach
    </select>
    <span class="form-bar"></span>
</div>

<div class="mt-3">
    <button class="btn btn-primary waves-effect waves-light">{{ $submitLabel }}</button>
    <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary">
        {{ __('settings::settings.back') }}
    </a>
</div>