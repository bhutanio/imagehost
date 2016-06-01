<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ url('/') }}" title="{{ env('SITE_NAME') }}">{{ env('SITE_NAME') }}</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="" title="{{ env('SITE_NAME') }}"><a href="{{ url('/') }}">Home</a></li>
            </ul>

            <ul class="nav navbar-nav pull-right">
                @if(auth()->check())
                    <li class="{{ request()->is('my') ? 'active' : ''  }}">
                        <a href="{{ url('my') }}" title="My Images">My Images</a>
                    </li>
                @else
                    <li class="{{ request()->is('auth/login') ? 'active' : ''  }}">
                        <a href="{{ url('auth/login') }}" title="Register">Login</a>
                    </li>
                    <li class="{{ request()->is('auth/register') ? 'active' : ''  }}">
                        <a href="{{ url('auth/register') }}" title="Register">Register</a>
                    </li>
                @endif

            </ul>
        </div>
    </div>
</nav>