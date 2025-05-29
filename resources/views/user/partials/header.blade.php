        <!-- top navigation -->
        <div class="top_nav">
            <div class="nav_menu">
                <div class="nav toggle">
                  <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                </div>
                <nav class="nav navbar-nav">
                <ul class=" navbar-right">
                  <li class="nav-item dropdown open" style="padding-left: 68px;">
                    <a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown" data-toggle="dropdown" aria-expanded="false">
                      <img src="{{ OtherHelpers::user_logo(Auth::user()->userDetail->logo) }}" alt="">{{ Auth::user()->userDetail['name'] }}
                    </a>
                    <div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">
                      <a class="dropdown-item"  href="{{ route('user.profile') }}"> Profile</a>
                        <a class="dropdown-item"  href="{{ route('user.change-password') }}">
                          
                          <span>Change Password</span>
                        </a>
                    
                      <a class="dropdown-item"  href="{{ route('logout') }}"><i class="fa fa-sign-out pull-right"></i> Log Out</a>
                    </div>
                  </li>

                  
                </ul>
              </nav>
            </div>
          </div>
          <!-- /top navigation -->
