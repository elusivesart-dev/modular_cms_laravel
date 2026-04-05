@extends('public-theme::layouts.public')

@section('hero')
    <section class="public-hero">
        <div class="container">
            <div class="row align-items-center g-4 py-5">
                <div class="col-lg-7">
                    <span class="badge rounded-pill text-bg-warning mb-3">{{ __('CMS Website Theme') }}</span>
                    <h1 class="display-5 fw-bold mb-3">
                        {{ __('Build pages, posts and dynamic content with one public theme.') }}
                    </h1>
                    <p class="lead text-light-emphasis mb-4">
                        {{ __('A free Bootstrap public theme designed for modular CMS websites, blogs and content-driven platforms.') }}
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ url('/blog') }}" class="btn btn-warning btn-lg">
                            {{ __('Explore Blog') }}
                        </a>
                        <a href="{{ url('/pages') }}" class="btn btn-outline-light btn-lg">
                            {{ __('Browse Pages') }}
                        </a>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="hero-feature-card shadow-lg">
                        <div class="small text-uppercase fw-semibold text-primary mb-2">{{ __('Quick Overview') }}</div>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3">• {{ __('Responsive Bootstrap 5 layout') }}</li>
                            <li class="mb-3">• {{ __('Blog, page and category templates') }}</li>
                            <li class="mb-3">• {{ __('User account dropdown') }}</li>
                            <li class="mb-3">• {{ __('Sidebar, search and footer blocks') }}</li>
                            <li>• {{ __('Ready for Blade + modular rendering') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="row g-4">
                @for($i = 1; $i <= 6; $i++)
                    <div class="col-md-6">
                        <article class="card h-100 border-0 shadow-sm post-card">
                            <img
                                src="https://picsum.photos/seed/post{{ $i }}/900/560"
                                class="card-img-top"
                                alt="Post {{ $i }}"
                            >
                            <div class="card-body">
                                <span class="badge text-bg-primary mb-2">{{ __('Featured') }}</span>
                                <h3 class="h5">
                                    <a href="{{ url('/blog/example-post-' . $i) }}" class="text-decoration-none text-dark">
                                        {{ __('Example content block') }} {{ $i }}
                                    </a>
                                </h3>
                                <p class="text-muted mb-3">
                                    {{ __('Use this area for dynamic CMS excerpts, featured posts, landing page sections or module widgets.') }}
                                </p>
                                <a href="{{ url('/blog/example-post-' . $i) }}" class="btn btn-outline-primary btn-sm">
                                    {{ __('Read more') }}
                                </a>
                            </div>
                        </article>
                    </div>
                @endfor
            </div>
        </div>

        <div class="col-lg-4">
            @include('public-theme::partials.sidebar')
        </div>
    </div>
@endsection