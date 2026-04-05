@extends('admin-theme::layouts.admin')

@section('title', __('core-localization::ui.language_management_title'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ __('core-localization::ui.language_management_title') }}</h5>
            <span>{{ __('core-localization::ui.language_management_description') }}</span>
        </div>

        <div class="card-block">
            <form method="POST" action="{{ route('localization.languages.default-locale.update') }}" class="mb-4">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="default_locale">{{ __('core-localization::ui.default_site_locale') }}</label>

                    <select
                        id="default_locale"
                        name="locale"
                        class="form-control @error('locale') is-invalid @enderror"
                    >
                        @foreach($availableLocales as $localeCode => $localeLabel)
                            <option value="{{ $localeCode }}" @selected($currentDefaultLocale === $localeCode)>
                                {{ $localeLabel }}
                            </option>
                        @endforeach
                    </select>

                    @error('locale')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary waves-effect waves-light">
                    {{ __('core-localization::ui.save_default_locale_button') }}
                </button>
            </form>

            <form
                method="POST"
                action="{{ route('localization.languages.upload') }}"
                enctype="multipart/form-data"
                class="mb-4"
            >
                @csrf

                <div class="form-group">
                    <label for="language_package">{{ __('core-localization::ui.upload_language_package') }}</label>

                    <input
                        id="language_package"
                        type="file"
                        name="language_package"
                        accept=".zip,application/zip"
                        class="form-control @error('language_package') is-invalid @enderror"
                    >

                    <small class="form-text text-muted">
                        {{ __('core-localization::ui.upload_language_package_hint') }}
                    </small>

                    @error('language_package')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary waves-effect waves-light">
                    {{ __('core-localization::ui.upload_language_button') }}
                </button>
            </form>

            @if($errors->has('language_management'))
                <div class="alert alert-danger">
                    {{ $errors->first('language_management') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('core-localization::ui.language_code') }}</th>
                            <th>{{ __('core-localization::ui.language_name_column') }}</th>
                            <th>{{ __('core-localization::ui.language_native_name_column') }}</th>
                            <th>{{ __('core-localization::ui.language_direction') }}</th>
                            <th>{{ __('core-localization::ui.language_version') }}</th>
                            <th>{{ __('core-localization::ui.language_type') }}</th>
                            <th>{{ __('core-localization::ui.language_path') }}</th>
                            <th>{{ __('core-localization::ui.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($languages as $language)
                            @php
                                $isCurrentDefault = $currentDefaultLocale === $language->code;
                                $isSystemLanguage = (bool) $language->is_system;
                                $canDelete = !$isSystemLanguage && !$isCurrentDefault;
                            @endphp

                            <tr>
                                <td>{{ strtoupper($language->code) }}</td>
                                <td>{{ $language->name }}</td>
                                <td>{{ $language->native_name }}</td>
                                <td>{{ strtoupper($language->direction) }}</td>
                                <td>{{ $language->version ?: '—' }}</td>
                                <td>
                                    @if($isSystemLanguage)
                                        <span class="badge badge-info">{{ __('core-localization::ui.system_language') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('core-localization::ui.custom_language') }}</span>
                                    @endif

                                    @if($isCurrentDefault)
                                        <span class="badge badge-success">{{ __('core-localization::ui.default_language_badge') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $language->installed_path ?: '—' }}</small>
                                </td>
                                <td>
                                    @if($canDelete)
                                        <form
                                            method="POST"
                                            action="{{ route('localization.languages.destroy', ['code' => $language->code]) }}"
                                            onsubmit="return confirm('{{ __('core-localization::ui.delete_language_confirm') }}');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger btn-sm waves-effect waves-light">
                                                {{ __('core-localization::ui.delete_language_button') }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">{{ __('core-localization::ui.delete_not_available') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    {{ __('core-localization::ui.no_languages_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection