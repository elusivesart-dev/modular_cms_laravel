@extends('public-theme::layouts.public')

@section('title', __('Category'))

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="mb-4">
                <div class="small text-uppercase text-muted fw-semibold mb-2">{{ __('Category Archive') }}</div>
                <h1 class="fw-bold mb-2">{{ __('News') }}</h1>
                <p class="text-muted mb-0">
                    {{ __('Posts filtered by category.') }}
                </p>
            </div>

            @for($i = 1; $i <= 6; $i++)
                <article class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="small text-muted mb-2">
                            {{ __('March') }} {{ 20 + $i }}, 2026
                        </div>
                        <h2 class="h4">
                            <a href="{{ url('/blog/category-post-' . $i) }}" class="text-decoration-none text-dark">
                                {{ __('Category Post') }} {{ $i }}
                            </a>
                        </h2>
                        <p class="text-muted mb-3">
                            {{ __('Use this archive template for category, taxonomy or tag-based content grouping.') }}
                        </p>
                        <a href="{{ url('/blog/category-post-' . $i) }}" class="btn btn-outline-primary btn-sm">
                            {{ __('Read more') }}
                        </a>
                    </div>
                </article>
            @endfor
        </div>

        <div class="col-lg-4">
            @include('public-theme::partials.sidebar')
        </div>
    </div>
@endsection