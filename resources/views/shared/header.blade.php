@include('shared.auth-navbar')
<header class="site-logo p-2">


    <nav class="navbar list-group navbar-expand-lg">

        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('img/cesi-logo.png') }}" alt="CESi" height="76">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">HOME</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Events
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('events', ['type' => 'month']) }}">This month</a>
                        <a class="dropdown-item" href="{{ route('events', ['type' => 'past']) }}">Past</a>
                        <a class="dropdown-item" href="{{ route('events', ['type' => 'future']) }}">Futur</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('events', ['type' => 'suggestions']) }}">IDEA BOX</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Boutique
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        {{-- <a class="dropdown-item" href="{{ route('products.index', ['category' => 'swagg']) }}">Swagg</a>
                        <a class="dropdown-item" href="{{route('products.index',['category'=>'goodies']) }}">Goodies</a>
                        --}}

                        @foreach ($categories as $item)
                        <a class="dropdown-item"
                            href="{{ route('products.index', ['category' => $item]) }}">{{$item}}</a>
                        @endforeach
                    </div>
                </li>
            </ul>
        </div>
    </nav>

</header>
