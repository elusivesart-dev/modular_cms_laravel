@extends('admin-theme::layouts.admin')

@section('title', __('settings::settings.title'))

@section('content')
    @if(!empty($groups) && count($groups) > 0)
        <div class="row">
            @foreach($groups as $group)
                <div class="col-xl-4 col-md-6 col-sm-12">
                    <div class="card mat-stat-card">
                        <div class="card-block">
                            <div class="row align-items-center b-b-default">
                                <div class="col-sm-3 text-center p-b-20 p-t-20 b-r-default">
                                    <i class="ti-settings text-c-blue f-24"></i>
                                </div>

                                <div class="col-sm-9 p-b-20 p-t-20">
                                    <h5 class="m-b-5">{{ $group['title'] }}</h5>
                                    <p class="text-muted m-b-0">
                                        {{ __('settings::settings.items_count', ['count' => $group['count']]) }}
                                    </p>
                                </div>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-12 p-t-20">
                                    <p class="text-muted m-b-20">
                                        {{ $group['description'] }}
                                    </p>

                                    <a
                                        href="{{ route('settings.group.edit', ['group' => $group['group']]) }}"
                                        class="btn btn-primary waves-effect waves-light btn-sm"
                                    >
                                        {{ __('settings::settings.edit_group') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-block text-center">
                        <p class="text-muted m-b-0">
                            {{ __('settings::settings.no_records') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection