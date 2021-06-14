<nav class="navbar navbar-expand-md bg-light user-navbar p-0 auth-navbar justify-content-end">

    <div class="navbar-container" id="authNavbar">
        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav">
            <!-- Authentication Links -->
            @guest
            <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">{{ __('Sign in') }}</a>
            </li>

            @if (Route::has('register'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('register') }}">{{ __('Sign up') }}</a>
            </li>
            @endif
            @else
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle mt-4" href="#" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    <img src="{{ get_gravatar(Auth::user()->email) }}" alt="Avatar" class="avatar">
                    {{ Auth::user()->fname }} {{ Auth::user()->lname }}

                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a href="{{ route('users.events') }}" class="dropdown-item">{{ __('My events') }}</a>
                    <a href="{{ route('cart') }}" class="dropdown-item">{{ __('Cart') }}</a>
                    @if (Auth::user()->hasRole('admin'))
                    <a href="{{ route('users.reports') }}" class="dropdown-item">{{ __('Reports') }}</a>
                    <a href="{{ route('users.index') }}" class="dropdown-item">{{ __('Users') }}</a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                        {{ __('Log out') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
            @endguest
        </ul>
    </div>
</nav>
