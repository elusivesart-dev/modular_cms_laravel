@extends('public-theme::layouts.public')

@section('title', __('Blog'))

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="mb-4">
                <h1 class="fw-bold mb-2">{{ __('Latest Posts') }}</h1>
                <p class="text-muted mb-0">
                    {{ __('A content-first blog layout suitable for modular CMS rendering.') }}
                </p>
            </div>

            @for($i = 1; $i <= 8; $i++)
                <article class="card border-0 shadow-sm mb-4 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img
                                src="https://picsum.photos/seed/blog{{ $i }}/900/700"
                                class="img-fluid h-100 object-fit-cover"
                                alt="Blog {{ $i }}"
                            >
                        </div>
                        <div class="col-md-8">
                            <div class="card-body p-4">
                                <div class="small text-muted mb-2">
                                    {{ __('March') }} {{ 30 - $i }}, 2026 · {{ __('Category') }} {{ $i }}
                                </div>
                                <h2 class="h4">
                                    <a href="{{ url('/blog/example-post-' . $i) }}" class="text-decoration-none text-dark">
                                        {{ __('Example Blog Post') }} {{ $i }}
                                    </a>
                                </h2>
                                <p class="text-muted mb-3">
                                    {{ __('This layout is ready for excerpt, category, author, publish date and slug-based routing.') }}
                                </p>
                                <a href="{{ url('/blog/example-post-' . $i) }}" class="btn btn-primary btn-sm">
                                    {{ __('Read article') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            @endfor

            <nav aria-label="Pagination">
                <ul class="pagination">
                    <li class="page-item disabled"><span class="page-link">{{ __('Previous') }}</span></li>
                    <li class="page-item active"><span class="page-link">1</span></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">{{ __('Next') }}</a></li>
                </ul>
            </nav>
        </div>

        <div class="col-lg-4">
            @include('public-theme::partials.sidebar')
        </div>
    </div>
@endsection