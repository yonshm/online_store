<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="{{ asset('/css/admin.css') }}" rel="stylesheet" />
    <title>@yield('title', 'Admin - Online Store')</title>
</head>

<body>
    <div class="row g-0">
        <!-- sidebar -->
        <div class="p-3 col fixed text-white bg-dark">
            <a href="{{ route('admin.home.index') }}" class="text-white text-decoration-none">
                <span class="fs-4">Admin Panel</span>
            </a>
            <hr />
            <ul class="nav flex-column">
                <li><a href="{{ route('admin.home.index') }}" class="nav-link text-white">- Admin - Home</a></li>
                <li><a href="{{ route('admin.product.index') }}" class="nav-link text-white">- Admin - Products</a></li>
                <li><a href="{{ route('admin.category.index') }}" class="nav-link text-white">- Admin - Categories</a>
                </li>
                <li><a href="{{ route('discounts.index') }}" class="nav-link text-white">- Admin - Discounts</a></li>
                <li><a href="{{ route('admin.order.index') }}" class="nav-link text-white">- Admin - Orders</a></li>
                <li>

                    <a href="{{ route('admin.supplier.index') }}" class="nav-link text-white">- Admin - Suppliers</a>

                </li>

                <li>
                    @if (auth()->check() && auth()->user()->role === 'super_admin')
                        <a href="{{ route('superAdmin.users.index') }}" class="nav-link text-white">- Admin - Admins</a>
                </li>
                @endif
                <li>
                    <a href="{{ route('home.index') }}" class="mt-2 btn bg-primary text-white">Go back to the home
                        page</a>

                </li>
            </ul>
        </div>
        <!-- sidebar -->
        <div class="col content-grey">
            <nav class="p-3 shadow text-end">
                <span class="profile-font">Admin</span>
                <img class="img-profile rounded-circle" src="{{ asset('/img/undraw_profile.svg') }}">
            </nav>

            <div class="g-0 m-5">
                @yield('content')
            </div>
        </div>
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
