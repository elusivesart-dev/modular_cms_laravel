@extends('public-theme::layouts.public')

@section('title', __('Page Title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">
            <article class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <div class="small text-uppercase text-muted fw-semibold mb-2">
                        {{ __('Static Page') }}
                    </div>

                    <h1 class="fw-bold mb-4">
                        {{ __('Page Title') }}
                    </h1>

                    <div class="content-body">
                        <p>
                            {{ __('This is the default static page template for your CMS. Use it for About, Terms, Privacy, Services and other module-rendered page content.') }}
                        </p>

                        <h2>{{ __('Section heading') }}</h2>
                        <p>
                            {{ __('Mauris sodales, felis at laoreet lacinia, lorem eros faucibus justo, vitae tincidunt erat velit nec neque.') }}
                        </p>

                        <h3>{{ __('Another content block') }}</h3>
                        <p>
                            {{ __('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.') }}
                        </p>

                        <ul>
                            <li>{{ __('Dynamic block support') }}</li>
                            <li>{{ __('Editor-ready content area') }}</li>
                            <li>{{ __('Responsive typography') }}</li>
                        </ul>
                    </div>
                </div>
            </article>
        </div>
    </div>
@endsection