<aside class="cms-sidebar">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ __('Search') }}</h5>
            <form method="GET" action="{{ url('/search') }}">
                <div class="input-group">
                    <input
                        type="text"
                        name="q"
                        class="form-control"
                        value="{{ request('q') }}"
                        placeholder="{{ __('Search posts...') }}"
                    >
                    <button class="btn btn-primary" type="submit">{{ __('Go') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ __('Categories') }}</h5>
            <ul class="list-unstyled mb-0">
                <li class="mb-2"><a href="{{ url('/category/news') }}" class="sidebar-link">{{ __('News') }}</a></li>
                <li class="mb-2"><a href="{{ url('/category/tutorials') }}" class="sidebar-link">{{ __('Tutorials') }}</a></li>
                <li class="mb-2"><a href="{{ url('/category/updates') }}" class="sidebar-link">{{ __('Updates') }}</a></li>
                <li><a href="{{ url('/category/events') }}" class="sidebar-link">{{ __('Events') }}</a></li>
            </ul>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ __('Recent posts') }}</h5>
            <ul class="list-unstyled mb-0">
                <li class="mb-3">
                    <a href="{{ url('/blog/example-post-1') }}" class="sidebar-link fw-semibold">
                        Example Post One
                    </a>
                    <div class="small text-muted">March 29, 2026</div>
                </li>
                <li class="mb-3">
                    <a href="{{ url('/blog/example-post-2') }}" class="sidebar-link fw-semibold">
                        Example Post Two
                    </a>
                    <div class="small text-muted">March 28, 2026</div>
                </li>
                <li>
                    <a href="{{ url('/blog/example-post-3') }}" class="sidebar-link fw-semibold">
                        Example Post Three
                    </a>
                    <div class="small text-muted">March 27, 2026</div>
                </li>
            </ul>
        </div>
    </div>
</aside>