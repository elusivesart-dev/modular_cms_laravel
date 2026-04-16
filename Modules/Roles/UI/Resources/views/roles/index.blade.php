@extends('admin-theme::layouts.admin')

@section('title', __('roles::roles.title'))

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
            <h5>{{ __('roles::roles.title') }}</h5>
            <div class="card-header-right">
                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm waves-effect waves-light">
                    {{ __('roles::roles.create') }}
                </a>
            </div>
        </div>

        <div class="card-block table-border-style">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>{{ __('roles::roles.name') }}</th>
                        <th>{{ __('roles::roles.slug') }}</th>
                        <th>{{ __('roles::roles.description') }}</th>
                        <th>{{ __('roles::roles.is_system') }}</th>
                        <th>{{ __('roles::roles.permissions') }}</th>
                        <th class="text-right">{{ __('roles::roles.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->display_name }}</td>
                            <td>{{ $role->slug }}</td>
                            <td>{{ $role->display_description }}</td>
                            <td>{{ $role->is_system ? __('roles::roles.yes') : __('roles::roles.no') }}</td>
                            <td>{{ (int) $role->permissions_count }}</td>
                            <td class="text-right">
                                <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-secondary waves-effect waves-light">
                                    {{ __('roles::roles.view') }}
                                </a>
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-primary waves-effect waves-light">
                                    {{ __('roles::roles.edit') }}
                                </a>
                                @if(!$role->is_system)
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger waves-effect waves-light">
                                            {{ __('roles::roles.delete') }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">{{ __('roles::roles.no_records') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div>
        {{ $roles->links() }}
    </div>
@endsection