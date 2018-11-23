       <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <!-- <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar">  </span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button> -->

        
                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        LiveShare
                    </a>
                </div>


                <ul class="nav navbar-nav navbar-left">
                            
                            <li><a  href="/livestream">LiveStream</a></li>
                            <li><a  href="/videocon">Video Conferencing</a></li>
                             <li><a  href="/broadcast">Multiple Video broadcast</a></li>
                             <li><a  href="/videochat">SimpleVideoChat</a></li>
                            <li><a class="nav-link" href="/features">Features</a></li>
                            <li><a class="nav-link" href="/contact">Contact</a></li> 
                            <li><a class="nav-link" href="/about">About</a></li>     
                    </ul>
               

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

   
