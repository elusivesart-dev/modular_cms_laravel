@extends('public-theme::layouts.public')

@section('title', __('Post Title'))

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <article class="card border-0 shadow-sm">
                <img
                    src="https://picsum.photos/seed/singlepost/1400/700"
                    class="card-img-top"
                    alt="Post image"
                >

                <div class="card-body p-4 p-lg-5">
                    <div class="small text-muted mb-2">
                        {{ __('March 29, 2026') }} · {{ __('News') }} · {{ __('By Admin') }}
                    </div>

                    <h1 class="fw-bold mb-4">
                        {{ __('Single post title goes here') }}
                    </h1>

                    <p class="lead text-muted">
                        {{ __('Use this template for article rendering, blog modules, announcements and content pages with rich text blocks.') }}
                    </p>

                    <p>
                        {{ __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse potenti. Integer euismod, dui eu volutpat lacinia, libero turpis feugiat massa, non feugiat justo augue at tortor.') }}
                    </p>

                    <p>
                        {{ __('Praesent convallis ultricies risus, et tristique odio lacinia a. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.') }}
                    </p>

                    <blockquote class="blockquote p-4 bg-light rounded-3 border-start border-4 border-primary my-4">
                        <p class="mb-0">
                            {{ __('This block is suitable for quotes, highlighted content or editor-rendered excerpts.') }}
                        </p>
                    </blockquote>

                    <p>
                        {{ __('Aliquam erat volutpat. Curabitur tempus efficitur mauris, vitae suscipit purus dictum sit amet. Donec pretium orci in commodo tempor.') }}
                    </p>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <span class="badge text-bg-secondary">cms</span>
                        <span class="badge text-bg-secondary">bootstrap</span>
                        <span class="badge text-bg-secondary">theme</span>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-lg-4">
            @include('public-theme::partials.sidebar')
        </div>
    </div>
@endsection