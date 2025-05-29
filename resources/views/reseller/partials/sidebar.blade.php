<div class="col-md-3 left_col">
	<div class="left_col scroll-view">
	  <div class="navbar nav_title" style="border: 0;">
		<a href="{{ route('reseller.index') }}" class="site_title"><i class="fa fa-paw"></i> <span>FC SMS</span></a>
	  </div>

	  <div class="clearfix"></div>

	  <!-- menu profile quick info -->
	  <div class="profile clearfix">
		<div class="profile_pic">
		  <img src="{{ OtherHelpers::user_logo(Auth::user()->userDetail->logo)  }}" alt="..." class="img-circle profile_img">
		</div>
		<div class="profile_info">
		  <span>Welcome,</span>
		  <h2>{{ \Illuminate\Support\Facades\Auth::user()->userDetail['name'] }}</h2>
		</div>
	  </div>
	  <!-- /menu profile quick info -->

	  <br />

	  <!-- sidebar menu -->
	  <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
		<div class="menu_section">
		  <h3>EURO {{ number_format(BalanceHelper::user_available_balance(Auth::id()), 2) }}</h3>
		  <ul class="nav side-menu">
			<li>
				<a href="{{ route('reseller.index') }}">
					<i class="fa fa-home"></i> Home
				</a>
			</li>
			<li><a><i class="menu-icon fa fa-users"></i> User <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
				  <li class="@yield('user_registration_menu_class')"><a href="{{ route('reseller.user.create') }}">User Registration</a></li>
				  <li class="@yield('user_list_menu_class')"><a href="{{ route('reseller.user.index') }}">User List</a></li>
				  <li class="@yield('suspend_user_menu_class')"><a href="{{ route('reseller.user.suspendUser') }}">Suspend User List</a></li>
				</ul>
			</li>
			<!--<li><a><i class="menu-icon fa fa-users"></i> Employee <span class="fa fa-chevron-down"></span></a>-->
			<!--	<ul class="nav child_menu">-->
			<!--	  <li class="@yield('employee_registration_menu_class')"><a href="{{ route('reseller.employee.create') }}">Employee Registration</a></li>-->
			<!--	  <li class="@yield('employee_list_menu_class')"><a href="{{ route('reseller.employee.index') }}">Employee List</a></li>-->
			<!--	  <li class="@yield('user_assign_to_employee')"><a href="{{ route('reseller.employee.asignUser') }}">Assign User</a></li>-->
			<!--	  <li class="@yield('change_employee')"><a href="{{ route('reseller.employee.change_employee') }}">Change Employee</a></li>-->
			<!--	  <li class="@yield('pay_to_employee')"><a href="{{ route('reseller.employee.pay_balance') }}">Pay to Employee</a></li>-->
			<!--	</ul>-->
			<!--</li>-->
			<li><a><i class="menu-icon fa fa-credit-card"></i> Price & Coverage <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
				  <li class="@yield('price_menu_class')"><a href="{{ route('reseller.priceList') }}">Price</a></li>
				</ul>
			</li>
			<!--<li>-->
			<!--	<a href="{{ route('reseller.sendSmsToAll') }}">-->
			<!--		<i class="menu-icon fa fa-calendar"></i> Send Notice-->
			<!--	</a>-->
			<!--</li>-->
			<li><a><i class="menu-icon  fa fa-book"></i> Accounts <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
				  <li class="@yield('add_fund_credit_menu_class')"><a href="{{ route('reseller.balance.credit.create') }}">Credit</a></li>
				  <li class="@yield('add_fund_debit_menu_class')"><a href="{{ route('reseller.balance.debit.create') }}">Debit</a></li>
				  <li class="@yield('user_statement_menu_class')"><a href="{{ route('reseller.transactionHistory') }}">Statements</a></li>
				</ul>
			</li>
			{{-- <li><a><i class="fa fa-edit"></i> Forms <span class="fa fa-chevron-down"></span></a>
			  <ul class="nav child_menu">
				<li><a href="form.html">General Form</a></li>
				<li><a href="form_advanced.html">Advanced Components</a></li>
				<li><a href="form_validation.html">Form Validation</a></li>
				<li><a href="form_wizards.html">Form Wizard</a></li>
				<li><a href="form_upload.html">Form Upload</a></li>
				<li><a href="form_buttons.html">Form Buttons</a></li>
			  </ul>
			</li>
			<li><a><i class="fa fa-desktop"></i> UI Elements <span class="fa fa-chevron-down"></span></a>
			  <ul class="nav child_menu">
				<li><a href="general_elements.html">General Elements</a></li>
				<li><a href="media_gallery.html">Media Gallery</a></li>
				<li><a href="typography.html">Typography</a></li>
				<li><a href="icons.html">Icons</a></li>
				<li><a href="glyphicons.html">Glyphicons</a></li>
				<li><a href="widgets.html">Widgets</a></li>
				<li><a href="invoice.html">Invoice</a></li>
				<li><a href="inbox.html">Inbox</a></li>
				<li><a href="calendar.html">Calendar</a></li>
			  </ul>
			</li>
			<li><a><i class="fa fa-table"></i> Tables <span class="fa fa-chevron-down"></span></a>
			  <ul class="nav child_menu">
				<li><a href="tables.html">Tables</a></li>
				<li><a href="tables_dynamic.html">Table Dynamic</a></li>
			  </ul>
			</li>
			<li><a><i class="fa fa-bar-chart-o"></i> Data Presentation <span class="fa fa-chevron-down"></span></a>
			  <ul class="nav child_menu">
				<li><a href="chartjs.html">Chart JS</a></li>
				<li><a href="chartjs2.html">Chart JS2</a></li>
				<li><a href="morisjs.html">Moris JS</a></li>
				<li><a href="echarts.html">ECharts</a></li>
				<li><a href="other_charts.html">Other Charts</a></li>
			  </ul>
			</li>
			<li><a><i class="fa fa-clone"></i>Layouts <span class="fa fa-chevron-down"></span></a>
			  <ul class="nav child_menu">
				<li><a href="fixed_sidebar.html">Fixed Sidebar</a></li>
				<li><a href="fixed_footer.html">Fixed Footer</a></li>
			  </ul>
			</li> --}}
		  </ul>
		</div>
		{{-- <div class="menu_section">
		  <h3>Live On</h3>
		  <ul class="nav side-menu">
			<li><a><i class="fa fa-bug"></i> Additional Pages <span class="fa fa-chevron-down"></span></a>
			  <ul class="nav child_menu">
				<li><a href="e_commerce.html">E-commerce</a></li>
				<li><a href="projects.html">Projects</a></li>
				<li><a href="project_detail.html">Project Detail</a></li>
				<li><a href="contacts.html">Contacts</a></li>
				<li><a href="profile.html">Profile</a></li>
			  </ul>
			</li>
			<li><a><i class="fa fa-windows"></i> Extras <span class="fa fa-chevron-down"></span></a>
			  <ul class="nav child_menu">
				<li><a href="page_403.html">403 Error</a></li>
				<li><a href="page_404.html">404 Error</a></li>
				<li><a href="page_500.html">500 Error</a></li>
				<li><a href="plain_page.html">Plain Page</a></li>
				<li><a href="login.html">Login Page</a></li>
				<li><a href="pricing_tables.html">Pricing Tables</a></li>
			  </ul>
			</li>
			<li><a><i class="fa fa-sitemap"></i> Multilevel Menu <span class="fa fa-chevron-down"></span></a>
			  <ul class="nav child_menu">
				  <li><a href="#level1_1">Level One</a>
				  <li><a>Level One<span class="fa fa-chevron-down"></span></a>
					<ul class="nav child_menu">
					  <li class="sub_menu"><a href="level2.html">Level Two</a>
					  </li>
					  <li><a href="#level2_1">Level Two</a>
					  </li>
					  <li><a href="#level2_2">Level Two</a>
					  </li>
					</ul>
				  </li>
				  <li><a href="#level1_2">Level One</a>
				  </li>
			  </ul>
			</li>                  
			<li><a href="javascript:void(0)"><i class="fa fa-laptop"></i> Landing Page <span class="label label-success pull-right">Coming Soon</span></a></li>
		  </ul>
		</div> --}}

	  </div>
	  <!-- /sidebar menu -->

	  <!-- /menu footer buttons -->
	  <div class="sidebar-footer hidden-small">
		<a data-toggle="tooltip" data-placement="top" title="Settings">
		  <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
		</a>
		<a data-toggle="tooltip" data-placement="top" title="FullScreen">
		  <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
		</a>
		<a data-toggle="tooltip" data-placement="top" title="Lock">
		  <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
		</a>
		<a data-toggle="tooltip" data-placement="top" title="Logout" href="{{ route('logout') }}">
		  <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
		</a>
	  </div>
	  <!-- /menu footer buttons -->
	</div>
  </div>