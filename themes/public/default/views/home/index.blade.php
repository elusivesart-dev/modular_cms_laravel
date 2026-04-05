@extends('public-theme::layouts.public')

@section('content')
<div class="card border-light mb-3" style="max-width: 20rem;">
  <div class="card-header">{{ app()->getLocale() === 'en' ? 'Welcome to the public site' : 'Добре дошли в публичната част' }}</div>
  <div class="card-body">
    <h4 class="card-title">{{ app()->getLocale() === 'en' ? 'Welcome to the public site' : 'Добре дошли в публичната част' }}</h4>
    <p class="card-text">{{ app()->getLocale() === 'en'
                    ? 'The public side now uses the public theme manager.'
                    : 'Публичната част вече използва public theme manager-а.' }}</p>
  </div>
</div>
@endsection