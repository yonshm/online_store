<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <link href="{{ asset('/css/app.css') }}" rel="stylesheet" />
  <title>@yield('title', 'Online Store')</title>
  @if(app()->getLocale() == 'ar')
    <style>
      body { direction: rtl; text-align: right; }
      .navbar-nav { flex-direction: row-reverse; }
      .ms-auto { margin-left: 0 !important; margin-right: auto !important; }
      .me-auto { margin-right: 0 !important; margin-left: auto !important; }
    </style>
  @endif
</head>
<body>
  <!-- header -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-secondary py-4">
    <div class="container">
      <a class="navbar-brand" href="{{ route('home.index') }}">Online Store</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
        aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav ms-auto">
          <a class="nav-link active" href="{{ route('home.index') }}">{{ __('Home') }}</a>
          <a class="nav-link active" href="{{ route('product.index') }}">{{ __('Products') }}</a>
          <a class="nav-link active" href="{{ route('cart.index') }}">{{ __('Cart') }}</a>
          <a class="nav-link active" href="{{ route('home.about') }}">{{ __('About') }}</a>
          
          <!-- Language Selector -->
          <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              {{ __('Language') }}
            </a>
            <ul class="dropdown-menu" aria-labelledby="languageDropdown">
              <li><a class="dropdown-item" href="{{ route('language.switch', 'en') }}">{{ __('English') }}</a></li>
              <li><a class="dropdown-item" href="{{ route('language.switch', 'fr') }}">{{ __('French') }}</a></li>
              <li><a class="dropdown-item" href="{{ route('language.switch', 'ar') }}">{{ __('Arabic') }}</a></li>
            </ul>
          </div>
          
          <div class="vr bg-white mx-2 d-none d-lg-block"></div>
          @guest
          <a class="nav-link active" href="{{ route('login') }}">{{ __('Login') }}</a>
          <a class="nav-link active" href="{{ route('register') }}">{{ __('Register') }}</a>
          @else
          <a class="nav-link active" href="{{ route('myaccount.orders') }}">{{ __('My Account') }}</a>
          <form id="logout" action="{{ route('logout') }}" method="POST">
            <a role="button" class="nav-link active"
               onclick="document.getElementById('logout').submit();">{{ __('Logout') }}</a>
            @csrf
          </form>
          @endguest
        </div>
      </div>
    </div>
  </nav>

  <header class="masthead bg-primary text-white text-center py-4">
    <div class="container d-flex align-items-center flex-column">
      <h2>@yield('subtitle', 'A Laravel Online Store')</h2>
    </div>
  </header>
  <!-- header -->

  <div class="container my-4">
    @yield('content')
  </div>

  <!-- footer -->
  <div class="copyright py-4 text-center text-white">
    <div class="container">
      <small>
        Copyright - <a class="text-reset fw-bold text-decoration-none" target="_blank"
          href="https://twitter.com/danielgarax">
          Daniel Correa
        </a> - <b>Paola Vallejo</b>
      </small>
    </div>
  </div>
  <!-- footer -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
  </script>
  @stack('scripts')
</body>
</html>
