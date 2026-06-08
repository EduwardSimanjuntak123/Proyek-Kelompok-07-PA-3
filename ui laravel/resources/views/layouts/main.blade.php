<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>@yield('title') | {{ $pengaturan->name ?? config('app.name') }}</title>

  {{-- Styling --}}
  @include('includes.style')
  @stack('style')
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
        {{-- Navbar --}}
        @include('partials.nav')


        {{-- Sidebar --}}
        @include('partials.sidebar')

      <!-- Main Content -->
      <div class="main-content">
        @yield('content')
      </div>

      @includeWhen(session('role') === 'Dosen' && in_array(1, session('dosen_roles', []), true) && !request()->routeIs('ai.kelompok'), 'partials.agent-float')

      {{-- Footer --}}
      @include('partials.footer')
    </div>
  </div>

  {{-- Scripts --}}
  @include('includes.script')
  @stack('script')
</body>
</html>
