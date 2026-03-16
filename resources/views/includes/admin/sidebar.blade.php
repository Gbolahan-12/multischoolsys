<nav class="sidebar">
  <div class="sidebar-header">
    <a @if (auth()->user()->role === 'admin') href="{{ route('admin.dashboard') }}" 
       @else href="{{ route('proprietor.dashboard') }}" @endif class="sidebar-brand">
      MultiSchool<span>Sys</span>
    </a>

    <div class="sidebar-toggler not-active">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>

  <div class="sidebar-body">
    <ul class="nav">

      <li class="nav-item nav-category">Main</li>

      <li class="nav-item {{ request()->routeIs('admin.dashboard') || request()->routeIs('proprietor.dashboard') ? 'active' : '' }}">
        @if(auth()->user()->role === 'admin')
          <a href="{{ route('admin.dashboard') }}" class="nav-link">
        @else
          <a href="{{ route('proprietor.dashboard') }}" class="nav-link">
        @endif
            <i class="link-icon" data-feather="box"></i>
            <span class="link-title">Dashboard</span>
          </a>
      </li>

      <li class="nav-item nav-category">Web Apps</li>

      <!-- Result -->
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('staff.results.*') ? '' : 'collapsed' }}"
           data-toggle="collapse"
           href="#emails"
           role="button"
           aria-expanded="{{ request()->routeIs('staff.results.*') ? 'true' : 'false' }}">
           
          <i class="link-icon" data-feather="book"></i>
          <span class="link-title">Result</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>

        <div class="collapse {{ request()->routeIs('staff.results.*') ? 'show' : '' }}" id="emails">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{ route('staff.results.index') }}"
                 class="nav-link {{ request()->routeIs('staff.results.index') ? 'active' : '' }}">
                 Results
              </a>
            </li>
          </ul>
        </div>
      </li>

      <!-- Students -->
      <li class="nav-item {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
        <a href="{{ route('admin.students.index') }}" class="nav-link">
          <i class="link-icon" data-feather="users"></i>
          <span class="link-title">Students</span>
        </a>
      </li>

      <!-- Staff -->
      <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <a href="{{ route('admin.users.index') }}" class="nav-link">
          <i class="link-icon" data-feather="user"></i>
          <span class="link-title">Staff</span>
        </a>
      </li>

      <!-- Payments -->
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.payments.*') || request()->routeIs('admin.paid-students.*') ? '' : 'collapsed' }}"
           data-toggle="collapse"
           href="#uiComponents"
           role="button"
           aria-expanded="{{ request()->routeIs('admin.payments.*') || request()->routeIs('admin.paid-students.*') ? 'true' : 'false' }}">
           
          <i class="link-icon" data-feather="credit-card"></i>
          <span class="link-title">Payment</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>

        <div class="collapse {{ request()->routeIs('admin.payments.*') || request()->routeIs('admin.paid-students.*') ? 'show' : '' }}" id="uiComponents">
          <ul class="nav sub-menu">

            <li class="nav-item">
              <a href="{{ route('admin.payments.create') }}"
                 class="nav-link {{ request()->routeIs('admin.payments.create') ? 'active' : '' }}">
                 Record Payment
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('admin.paid-students.index') }}"
                 class="nav-link {{ request()->routeIs('admin.paid-students.index') ? 'active' : '' }}">
                 Completed
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('admin.payments.defaulter.index') }}"
                 class="nav-link {{ request()->routeIs('admin.payments.defaulter.*') ? 'active' : '' }}">
                 Optional
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('admin.payments.index') }}"
                 class="nav-link {{ request()->routeIs('admin.payments.index') ? 'active' : '' }}">
                 All Payments
              </a>
            </li>

          </ul>
        </div>
      </li>

      <li class="nav-item nav-category">Components</li>

      <!-- Classes -->
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.classes.*') || request()->routeIs('admin.fees.*') ? '' : 'collapsed' }}"
           data-toggle="collapse"
           href="#advancedUI"
           role="button"
           aria-expanded="{{ request()->routeIs('admin.classes.*') || request()->routeIs('admin.fees.*') ? 'true' : 'false' }}">

          <i class="link-icon" data-feather="home"></i>
          <span class="link-title">Classes</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>

        <div class="collapse {{ request()->routeIs('admin.classes.*') || request()->routeIs('admin.fees.*') ? 'show' : '' }}" id="advancedUI">
          <ul class="nav sub-menu">

            <li class="nav-item">
              <a href="{{ route('admin.classes.index') }}"
                 class="nav-link {{ request()->routeIs('admin.classes.index') ? 'active' : '' }}">
                 Class List
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('admin.fees.index') }}"
                 class="nav-link {{ request()->routeIs('admin.fees.*') ? 'active' : '' }}">
                 Class Fee
              </a>
            </li>

          </ul>
        </div>
      </li>

    </ul>
  </div>
</nav>