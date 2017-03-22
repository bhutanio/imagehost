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
            {{--<ul class="nav navbar-nav">--}}
                {{--<li class="" title="{{ env('SITE_NAME') }}"><a href="{{ url('/') }}">Home</a></li>--}}
            {{--</ul>--}}

            <ul class="nav navbar-nav pull-right">
                @if(auth()->check())
                    <li class="dropdown{{ (request()->is('my*')) ? ' active' : ''  }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ auth()->user()->name }}
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ url('my') }}" title="My Uploads">My Uploads</a></li>
                            @if(auth()->id() == 2)
                                <li role="separator" class="divider"></li>
                                <li><a href="{{ url('admin') }}"><i class="fa fa-gears"></i> Admin Panel</a></li>
                            @endif
                            <li role="separator" class="divider"></li>
                            <li>
                                <a href="{{ url('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> Logout</a>
                                <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="{{ request()->is('login') ? 'active' : ''  }}">
                        <a href="{{ url('login') }}" title="Register">Login</a>
                    </li>
                    <li class="{{ request()->is('register') ? 'active' : ''  }}">
                        <a href="{{ url('register') }}" title="Register">Register</a>
                    </li>
                @endif

            </ul>
        </div>
    </div>
</nav>