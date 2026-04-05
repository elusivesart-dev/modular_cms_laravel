@extends('admin-theme::layouts.admin')

@section('title', __('settings::settings.groups.' . $group . '.title'))

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h5>{{ __('settings::settings.groups.' . $group . '.title') }}</h5>
        <span>{{ __('settings::settings.groups.' . $group . '.description') }}</span>
    </div>

    <div class="card-block">
        <form method="POST" action="{{ route('settings.group.update', ['group' => $group]) }}">
            @csrf
            @method('PUT')

            @forelse($settings as $setting)
                <div class="form-group form-primary">
                    <label for="setting_{{ $setting->id }}">
                        {{ $setting->label ?: $setting->key }}
                    </label>

                    @if($setting->description)
                        <small class="form-text text-muted mb-2">{{ $setting->description }}</small>
                    @endif

                    @if($setting->key === 'system.default_locale')
                        <select
                            id="setting_{{ $setting->id }}"
                            class="form-control @error('values.' . $setting->key) is-invalid @enderror"
                            name="values[{{ $setting->key }}]"
                        >
                            @foreach(($availableLocales ?? []) as $localeCode => $localeLabel)
                                <option
                                    value="{{ $localeCode }}"
                                    @selected(old('values.' . $setting->key, $setting->value) === $localeCode)
                                >
                                    {{ $localeLabel }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($setting->type === 'text')
                        <textarea
                            id="setting_{{ $setting->id }}"
                            class="form-control @error('values.' . $setting->key) is-invalid @enderror"
                            name="values[{{ $setting->key }}]"
                            rows="4"
                        >{{ old('values.' . $setting->key, $setting->value) }}</textarea>
                    @elseif($setting->type === 'boolean')
                        <select
                            id="setting_{{ $setting->id }}"
                            class="form-control @error('values.' . $setting->key) is-invalid @enderror"
                            name="values[{{ $setting->key }}]"
                        >
                            <option value="1" @selected(old('values.' . $setting->key, $setting->value) == '1')>
                                {{ __('core-localization::web.yes') }}
                            </option>
                            <option value="0" @selected(old('values.' . $setting->key, $setting->value) == '0')>
                                {{ __('core-localization::web.no') }}
                            </option>
                        </select>
                    @else
                        <input
                            id="setting_{{ $setting->id }}"
                            class="form-control @error('values.' . $setting->key) is-invalid @enderror"
                            type="text"
                            name="values[{{ $setting->key }}]"
                            value="{{ old('values.' . $setting->key, $setting->value) }}"
                        >
                    @endif

                    @error('values.' . $setting->key)
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <span class="form-bar"></span>
                </div>
            @empty
                <div class="text-muted">{{ __('settings::settings.no_records') }}</div>
            @endforelse

            <div class="mt-3">
                <button class="btn btn-primary waves-effect waves-light">
                    {{ __('settings::settings.save') }}
                </button>

                <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary">
                    {{ __('settings::settings.back') }}
                </a>
            </div>
        </form>
    </div>
</div>

@include('settings::settings.partials.language-management')

@endsection