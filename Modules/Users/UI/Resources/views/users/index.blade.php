@extends('admin-theme::layouts.admin')

@section('title', __('users::users.title'))

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
    <div class="card-header d-flex justify-content-between">
        <h5>{{ __('users::users.title') }}</h5>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm waves-effect waves-light">
            {{ __('users::users.create') }}
        </a>
    </div>

    <div class="card-block table-border-style">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>{{ __('users::users.name') }}</th>
                    <th>{{ __('users::users.email') }}</th>
                    <th>{{ __('users::users.is_active') }}</th>
                    <th class="text-right">{{ __('core-localization::web.actions') }}</th>
                </tr>
                </thead>

                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->is_active ? __('core-localization::web.yes') : __('core-localization::web.no') }}</td>
                        <td class="text-right">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-secondary waves-effect waves-light">
                                {{ __('users::users.show') }}
                            </a>

                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary waves-effect waves-light">
                                {{ __('users::users.edit') }}
                            </a>

                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="btn btn-sm btn-danger waves-effect waves-light"
                                    onclick="return confirm('{{ __('users::users.delete_confirm') }}')"
                                >
                                    {{ __('users::users.delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">{{ __('users::users.no_records') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links() }}
    </div>
</div>

@endsection