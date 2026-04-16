@extends('admin-theme::layouts.admin')

@section('title', __('permissions::permissions.title'))

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5>{{ __('permissions::permissions.title') }}</h5>

            <div class="card-header-right">
                @can('create', \Modules\Permissions\Infrastructure\Models\Permission::class)
                    <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm waves-effect waves-light">
                        {{ __('permissions::permissions.actions.create') }}
                    </a>
                @endcan
            </div>
        </div>

        <div class="card-block table-border-style">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('permissions::permissions.fields.name') }}</th>
                            <th>{{ __('permissions::permissions.fields.label') }}</th>
                            <th>{{ __('permissions::permissions.fields.description') }}</th>
                            <th>{{ __('permissions::permissions.fields.roles') }}</th>
                            <th class="text-right">{{ __('permissions::permissions.fields.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->display_label }}</td>
                                <td>{{ $permission->display_description ?: '—' }}</td>
                                <td>{{ $permission->roles_count }}</td>
                                <td class="text-right">
                                    @can('update', $permission)
                                        <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-primary waves-effect waves-light">
                                            {{ __('permissions::permissions.actions.edit') }}
                                        </a>
                                    @endcan

                                    @can('delete', $permission)
                                        <form action="{{ route('permissions.destroy', $permission) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger waves-effect waves-light"
                                                onclick="return confirm('{{ __('permissions::permissions.messages.confirm_delete') }}')"
                                            >
                                                {{ __('permissions::permissions.actions.delete') }}
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">{{ __('permissions::permissions.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div>
        {{ $permissions->links() }}
    </div>
@endsection