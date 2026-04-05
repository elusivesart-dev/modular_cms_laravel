@extends('admin-theme::layouts.admin')

@section('title', __('media::media.title'))

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('core-localization::web.close') }}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('core-localization::web.close') }}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $errors->first() }}
        <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('core-localization::web.close') }}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="row">
    @can('create', \Modules\Media\Infrastructure\Models\Media::class)
        <div class="col-xl-4 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ $pickerMode ? __('media::media.picker.title') : __('media::media.upload_title') }}</h5>
                </div>

                <div class="card-block">
                    <form
                        method="POST"
                        action="{{ route('media.store', $pickerMode ? ['picker' => 1, 'target' => $pickerTarget] : []) }}"
                        enctype="multipart/form-data"
                    >
                        @csrf

                        <div class="form-group form-primary">
                            <label for="file">{{ __('media::media.fields.file') }}</label>
                            <input type="file" name="file" id="file" class="form-control" required>
                            <span class="form-bar"></span>
                            @error('file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group form-primary">
                            <label for="title">{{ __('media::media.fields.title') }}</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}">
                            <span class="form-bar"></span>
                            @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group form-primary">
                            <label for="alt_text">{{ __('media::media.fields.alt_text') }}</label>
                            <input type="text" name="alt_text" id="alt_text" class="form-control" value="{{ old('alt_text') }}">
                            <span class="form-bar"></span>
                            @error('alt_text') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        @if($pickerMode)
                            <input type="hidden" name="picker" value="1">
                            <input type="hidden" name="target" value="{{ $pickerTarget }}">
                        @endif

                        <div class="alert alert-info mb-3">
                            {{ __('media::media.hints.allowed_types') }}:
                            {{ implode(', ', (array) config('media.allowed_extensions', [])) }}
                            <br>
                            {{ __('media::media.hints.max_size') }}:
                            {{ (int) config('media.max_file_size', 10240) / 1024 }} MB
                        </div>

                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            {{ __('media::media.actions.upload') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    <div class="col-xl-8 col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>{{ $pickerMode ? __('media::media.picker.library_title') : __('media::media.library_title') }}</h5>
            </div>

            <div class="card-block">
                <form method="GET" action="{{ route('media.index') }}" class="mb-4">
                    @if($pickerMode)
                        <input type="hidden" name="picker" value="1">
                        <input type="hidden" name="target" value="{{ $pickerTarget }}">
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-primary">
                                <label for="search">{{ __('media::media.filters.search') }}</label>
                                <input
                                    type="text"
                                    id="search"
                                    name="search"
                                    class="form-control"
                                    value="{{ $filters['search'] ?? '' }}"
                                >
                                <span class="form-bar"></span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group form-primary">
                                <label for="mime_type">{{ __('media::media.filters.type') }}</label>
                                <select id="mime_type" name="mime_type" class="form-control">
                                    <option value="">{{ __('media::media.filters.all') }}</option>
                                    <option value="image" {{ ($filters['mime_type'] ?? '') === 'image' ? 'selected' : '' }}>
                                        {{ __('media::media.types.image') }}
                                    </option>
                                    <option value="video" {{ ($filters['mime_type'] ?? '') === 'video' ? 'selected' : '' }}>
                                        {{ __('media::media.types.video') }}
                                    </option>
                                    <option value="audio" {{ ($filters['mime_type'] ?? '') === 'audio' ? 'selected' : '' }}>
                                        {{ __('media::media.types.audio') }}
                                    </option>
                                    <option value="application" {{ ($filters['mime_type'] ?? '') === 'application' ? 'selected' : '' }}>
                                        {{ __('media::media.types.document') }}
                                    </option>
                                </select>
                                <span class="form-bar"></span>
                            </div>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block waves-effect waves-light">
                                {{ __('media::media.actions.filter') }}
                            </button>
                        </div>
                    </div>
                </form>

                <div class="row">
                    @forelse($mediaItems as $item)
                        <div class="col-md-6 col-xl-4">
                            <div class="card mb-3">
                                <div class="card-block">
                                    <div class="text-center mb-3">
                                        @if(str_starts_with($item->mime_type, 'image/') && $item->url !== null)
                                            <img
                                                src="{{ $item->url }}"
                                                alt="{{ $item->alt_text ?? $item->original_name }}"
                                                class="img-fluid rounded"
                                                style="max-height: 160px; object-fit: cover;"
                                            >
                                        @else
                                            <div class="p-4 border rounded bg-light">
                                                <i class="ti-files" style="font-size: 48px;"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <h6 class="mb-1 text-break">{{ $item->title ?: $item->original_name }}</h6>
                                    <p class="text-muted small mb-1">{{ $item->mime_type }}</p>
                                    <p class="text-muted small mb-1">{{ $item->human_size }}</p>
                                    <p class="text-muted small mb-2">{{ $item->created_at?->format('d.m.Y H:i') }}</p>

                                    @if($item->url !== null)
                                        <a
                                            href="{{ $item->url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="btn btn-sm btn-secondary waves-effect waves-light"
                                        >
                                            {{ __('media::media.actions.open') }}
                                        </a>
                                    @endif

                                    @if($pickerMode && str_starts_with($item->mime_type, 'image/'))
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary waves-effect waves-light"
                                            onclick="selectMedia('{{ $pickerTarget }}', '{{ (int) $item->getKey() }}', '{{ $item->url }}')"
                                        >
                                            {{ __('media::media.actions.select') }}
                                        </button>
                                    @endif

                                    @can('delete', $item)
                                        <form
                                            action="{{ route('media.destroy', $pickerMode ? ['media' => $item, 'picker' => 1, 'target' => $pickerTarget] : ['media' => $item]) }}"
                                            method="POST"
                                            class="d-inline"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            @if($pickerMode)
                                                <input type="hidden" name="picker" value="1">
                                                <input type="hidden" name="target" value="{{ $pickerTarget }}">
                                            @endif
                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger waves-effect waves-light"
                                                onclick="return confirm('{{ __('media::media.messages.delete_confirm') }}')"
                                            >
                                                {{ __('media::media.actions.delete') }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                {{ __('media::media.messages.no_records') }}
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-3">
                    {{ $mediaItems->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@if($pickerMode)
    <script>
        function selectMedia(targetId, mediaId, mediaUrl) {
            if (window.opener && typeof window.opener.setMediaPickerValue === 'function') {
                window.opener.setMediaPickerValue(targetId, mediaId, mediaUrl);
                window.close();
            }
        }
    </script>
@endif

@endsection