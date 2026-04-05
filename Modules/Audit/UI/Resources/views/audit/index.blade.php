@extends('admin-theme::layouts.admin')

@section('title', __('audit::audit.title'))

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

    <div class="card">
        <div class="card-header">
            <h5>{{ __('audit::audit.title') }}</h5>
        </div>

        <div class="card-block">
            <form method="GET" action="{{ route('audit.index') }}">
                <div class="row">
                    <div class="col-lg-3 col-md-4">
                        <div class="form-group form-primary">
                            <label class="form-label">{{ __('audit::audit.event') }}</label>
                            <input type="text" name="event" class="form-control" value="{{ $filters['event'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-5">
                        <div class="form-group form-primary">
                            <label class="form-label">{{ __('audit::audit.search') }}</label>
                            <input type="text" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary waves-effect waves-light btn-block">
                                {{ __('audit::audit.filter') }}
                            </button>
                            <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary btn-block mt-2">
                                {{ __('audit::audit.reset') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('audit.bulk-delete') }}" id="bulk-delete-form">
        @csrf
        @method('DELETE')

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('audit::audit.title') }}</h5>

                <div id="bulk-actions" class="d-none">
                    <button
                        type="submit"
                        class="btn btn-danger btn-sm waves-effect waves-light"
                        onclick="return confirm('{{ __('audit::audit.confirm_delete_selected') }}')"
                    >
                        <span id="delete-selected-label">
                            {{ __('audit::audit.delete_selected') }} (0)
                        </span>
                    </button>
                </div>
            </div>

            <div class="card-block table-border-style">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>{{ __('audit::audit.id') }}</th>
                                <th>{{ __('audit::audit.author') }}</th>
                                <th>{{ __('audit::audit.action') }}</th>
                                <th>{{ __('audit::audit.user') }}</th>
                                <th>{{ __('audit::audit.role') }}</th>
                                <th>{{ __('audit::audit.ip_address') }}</th>
                                <th>{{ __('audit::audit.created_at') }}</th>
                                <th>{{ __('audit::audit.details') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $entry)
                                <tr>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="ids[]"
                                            value="{{ $entry['id'] }}"
                                            class="row-checkbox"
                                        >
                                    </td>
                                    <td>{{ $entry['id'] }}</td>
                                    <td>{{ $entry['author'] }}</td>
                                    <td>{{ $entry['action'] }}</td>
                                    <td>{{ $entry['user'] }}</td>
                                    <td>{{ $entry['role'] }}</td>
                                    <td>{{ $entry['ip_address'] }}</td>
                                    <td>{{ $entry['created_at'] }}</td>
                                    <td>
                                        <div class="mb-2 text-muted small">
                                            @forelse($entry['details'] as $detail)
                                                <div>{{ $detail }}</div>
                                            @empty
                                                <div>—</div>
                                            @endforelse
                                        </div>

                                        <form action="{{ route('audit.destroy', $entry['model']) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger waves-effect waves-light"
                                                onclick="return confirm('{{ __('audit::audit.confirm_delete') }}')"
                                            >
                                                {{ __('audit::audit.delete') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        {{ __('audit::audit.no_records') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>

    <div>
        {{ $logs->links() }}
    </div>

    <script>
        (function () {
            const selectAll = document.getElementById('select-all');
            const rowCheckboxes = Array.from(document.querySelectorAll('.row-checkbox'));
            const bulkActions = document.getElementById('bulk-actions');
            const deleteLabel = document.getElementById('delete-selected-label');

            function updateBulkActions() {
                const checked = rowCheckboxes.filter(checkbox => checkbox.checked).length;

                if (deleteLabel) {
                    deleteLabel.textContent = '{{ __('audit::audit.delete_selected') }} (' + checked + ')';
                }

                if (checked > 0) {
                    bulkActions.classList.remove('d-none');
                } else {
                    bulkActions.classList.add('d-none');
                }

                if (rowCheckboxes.length > 0) {
                    selectAll.checked = checked === rowCheckboxes.length;
                    selectAll.indeterminate = checked > 0 && checked < rowCheckboxes.length;
                } else {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    rowCheckboxes.forEach(checkbox => {
                        checkbox.checked = selectAll.checked;
                    });

                    updateBulkActions();
                });
            }

            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });

            updateBulkActions();
        })();
    </script>
@endsection