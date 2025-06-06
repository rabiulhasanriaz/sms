<div class="col-md-3 left_col">
	<div class="left_col scroll-view">
	  <div class="navbar nav_title" style="border: 0;">
		<a href="{{ route('admin.index') }}" class="site_title"><i class="fa fa-paw"></i> <span>FC SMS</span></a>
	  </div>

	  <div class="clearfix"></div>

	  <!-- menu profile quick info -->
	  <div class="profile clearfix">
		<div class="profile_pic">
		  <img src="{{ OtherHelpers::user_logo(Auth::user()->userDetail->logo)  }}" alt="..." class="img-circle profile_img">
		</div>
		<div class="profile_info">
		  <span>Welcome,</span>
		  <h2>{{ Auth::user()->userDetail['name'] }}</h2>
		</div>
	  </div>
	  <!-- /menu profile quick info -->

	  <br />

	  <!-- sidebar menu -->
	  <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
		<div class="menu_section">
		  <h3>General</h3>
		  <ul class="nav side-menu">
			<li class="@yield('dashboard_menu_class')">
				<a href="{{ route('admin.index') }}">
					<i class="fa fa-home"></i>
					 Home
				</a>

			</li>
			<li class="@yield('pending_sms_menu_class')">
				<a href="{{ route('admin.pending-campaign-sms') }}">
					<i class="fa fa-home"></i>
					 Pending Campaign
				</a>

			</li>
			<li class="@yield('api_comission_class')">
				<a href="{{ route('admin.api-permission') }}">
					<i class="fa fa-home"></i>
					 API Permission
				</a>

			</li>
              <li class="@yield('api_add_class')">
                  <a href="{{ route('admin.api-add') }}">
                      <i class="fa fa-home"></i>
                      API Add
                  </a>

              </li>
			<li class="@yield('dynamic_permission_class')">
				<a href="{{ route('admin.dynamic-permission') }}">
					<i class="fa fa-home"></i>
					 Dynamic Permission
				</a>

			</li>

			<li><a><i class="fa fa-edit"></i> English <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
				  <li class="@yield('route_register_class')"><a href="{{ route('admin.english.route-registers') }}">Route Registration</a></li>
				  <li class="@yield('assign_route_class')"><a href="{{ route('admin.english.assign-route') }}">Assign Route</a></li>
				</ul>
			</li>

			<li><a><i class="fa fa-edit"></i> Reseller <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
				  <li class="@yield('reseller_registration_menu_class')"><a href="{{ route('admin.reseller.create') }}">Reseller Registration</a></li>
				  <li class="@yield('reseller_list_menu_class')"><a href="{{ route('admin.reseller.index') }}">Reseller List</a></li>
				  <li class="@yield('reseller_tree_menu_class')"><a href="{{ route('admin.reseller.tree') }}">Reseller Tree</a></li>
				</ul>
			</li>
			<li><a><i class="fa fa-edit"></i> Reseller A/C Limit <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
				  <li class="@yield('limit_apply_menu_class')"><a href="{{ route('admin.reseller.limitApply') }}">Limit Apply</a></li>
				</ul>
			</li>
			<li><a><i class="fa fa-edit"></i> Accounts <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
				  <li class="@yield('add_fund_credit_menu_class')"><a href="{{ route('admin.balance.credit.create') }}">Add Fund Credit</a></li>
				  <li class="@yield('add_fund_debit_menu_class')"><a href="{{ route('admin.balance.debit.create') }}">Add Fund Debit</a></li>
				</ul>
			</li>
			<li><a><i class="fa fa-edit"></i> Phone Book <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
				  <li class="@yield('category_contact_menu_class')"><a href="{{ route('admin.categoryContact.index') }}">Category Contact</a></li>
				</ul>
			</li>
			<li class="@yield('all_logged_in_users_menu')">
				<a href="{{ route('admin.loggedInUsers') }}">
					<i class="fa fa-home"></i>
					 Logged Users
				</a>

			</li>
			<li><a><i class="fa fa-edit"></i> Report <span class="fa fa-chevron-down"></span></a>
			  <ul class="nav child_menu">
				<li class="@yield('user_report_class')">
					<a href="{{ route('admin.reports.user-reports') }}">User Report</a>
				</li>
			  </ul>
			</li>
			<li><a><i class="fa fa-edit"></i> Template <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
				  <li class="@yield('template_create_class')">
					  <a href="{{ route('admin.template.template-create') }}">Create</a>
				  </li>
				  <li class="@yield('template_assign_class')">
					<a href="{{ route('admin.template.template-assign') }}">Assign</a>
				</li>

				 <li class="@yield('dateFormat_class')">
					<a href="{{ route('admin.template.date-format') }}">Date Format</a>
				  </li>
				  <li class="@yield('date_assign_class')">
					<a href="{{ route('admin.template.date-format-assign') }}">Date Format Assign</a>
				  </li>
				</ul>
			  </li>
			<li><a><i class="fa fa-edit"></i> Sensitive Operations <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
				  <li class="@yield('extra_operation_menu_class')"><a href="{{ route('admin.changeBackground') }}">Login Background</a></li>
				  <li class="@yield('delete_data_menu_class')"><a href="{{ route('admin.deleteDataBeforeOneMonth') }}">Delete Data</a></li>
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
			</li> --}}
			{{-- <li><a><i class="fa fa-desktop"></i> UI Elements <span class="fa fa-chevron-down"></span></a>
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
			</li> --}}
			{{-- <li><a><i class="fa fa-table"></i> Tables <span class="fa fa-chevron-down"></span></a>
			  <ul class="nav child_menu">
				<li><a href="tables.html">Tables</a></li>
				<li><a href="tables_dynamic.html">Table Dynamic</a></li>
			  </ul>
			</li> --}}
			{{-- <li><a><i class="fa fa-bar-chart-o"></i> Data Presentation <span class="fa fa-chevron-down"></span></a>
			  <ul class="nav child_menu">
				<li><a href="chartjs.html">Chart JS</a></li>
				<li><a href="chartjs2.html">Chart JS2</a></li>
				<li><a href="morisjs.html">Moris JS</a></li>
				<li><a href="echarts.html">ECharts</a></li>
				<li><a href="other_charts.html">Other Charts</a></li>
			  </ul>
			</li> --}}
			{{-- <li><a><i class="fa fa-clone"></i>Layouts <span class="fa fa-chevron-down"></span></a>
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
