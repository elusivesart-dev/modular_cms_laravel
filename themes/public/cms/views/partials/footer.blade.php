<footer class="public-footer border-top">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5 class="fw-bold mb-3">{{ $siteName }}</h5>
                <p class="text-muted mb-0">
                    {{ function_exists('settings') ? settings('general.site_description', 'Modular CMS Website') : 'Modular CMS Website' }}
                </p>
            </div>

            <div class="col-lg-2 col-md-4">
                <h6 class="fw-semibold mb-3">{{ __('Navigation') }}</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="{{ url('/') }}" class="footer-link">{{ __('Home') }}</a></li>
                    <li class="mb-2"><a href="{{ url('/blog') }}" class="footer-link">{{ __('Blog') }}</a></li>
                    <li class="mb-2"><a href="{{ url('/pages') }}" class="footer-link">{{ __('Pages') }}</a></li>
                    <li><a href="{{ url('/contact') }}" class="footer-link">{{ __('Contact') }}</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-4">
                <h6 class="fw-semibold mb-3">{{ __('Categories') }}</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="{{ url('/category/news') }}" class="footer-link">{{ __('News') }}</a></li>
                    <li class="mb-2"><a href="{{ url('/category/tutorials') }}" class="footer-link">{{ __('Tutorials') }}</a></li>
                    <li class="mb-2"><a href="{{ url('/category/updates') }}" class="footer-link">{{ __('Updates') }}</a></li>
                    <li><a href="{{ url('/category/events') }}" class="footer-link">{{ __('Events') }}</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-4">
                <h6 class="fw-semibold mb-3">{{ __('Newsletter') }}</h6>
                <form method="POST" action="{{ url('/newsletter/subscribe') }}">
                    @csrf
                    <div class="mb-3">
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="{{ __('Your email address') }}"
                            required
                        >
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        {{ __('Subscribe') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-4 border-top small text-muted">
            <div>
                &copy; {{ now()->year }} {{ $siteName }}. {{ __('All rights reserved.') }}
            </div>
            <div>
                {{ __('Powered by modular CMS') }}
            </div>
        </div>
    </div>
</footer>