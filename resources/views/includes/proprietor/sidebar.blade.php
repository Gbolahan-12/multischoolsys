<nav class="sidebar">
  <div class="sidebar-header">
    <a @if (auth()->user()->role === 'admin')
        href="{{ route('admin.dashboard') }}"
       @else
        href="{{ route('proprietor.dashboard') }}"
       @endif class="sidebar-brand">
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

      <!-- Dashboard -->
      <li class="nav-item {{ request()->routeIs('proprietor.dashboard') ? 'active' : '' }}">
        <a href="{{ route('proprietor.dashboard') }}" class="nav-link">
          <i class="link-icon" data-feather="box"></i>
          <span class="link-title">Dashboard</span>
        </a>
      </li>

      <li class="nav-item nav-category">Web Apps</li>

      <!-- Session -->
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('proprietor.sessions.*') || request()->routeIs('admin.students.import.*') ? '' : 'collapsed' }}"
           data-toggle="collapse"
           href="#emails"
           role="button"
           aria-expanded="{{ request()->routeIs('proprietor.sessions.*') || request()->routeIs('admin.students.import.*') ? 'true' : 'false' }}">

          <i class="link-icon" data-feather="calendar"></i>
          <span class="link-title">Session</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>

        <div class="collapse {{ request()->routeIs('proprietor.sessions.*') || request()->routeIs('admin.students.import.*') ? 'show' : '' }}" id="emails">
          <ul class="nav sub-menu">

            <li class="nav-item">
              <a href="{{ route('proprietor.sessions.index') }}"
                 class="nav-link {{ request()->routeIs('proprietor.sessions.*') ? 'active' : '' }}">
                 Session
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('admin.students.import.form') }}"
                 class="nav-link {{ request()->routeIs('admin.students.import.*') ? 'active' : '' }}">
                 Upload Students
              </a>
            </li>

          </ul>
        </div>
      </li>

      <!-- Staff -->
      <li class="nav-item {{ request()->routeIs('proprietor.users.*') ? 'active' : '' }}">
        <a href="{{ route('proprietor.users.index') }}" class="nav-link">
          <i class="link-icon" data-feather="users"></i>
          <span class="link-title">Staff</span>
        </a>
      </li>

      <li class="nav-item nav-category">Components</li>

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
                 Make Payment
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

      <!-- Classes -->
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.classes.*') || request()->routeIs('admin.class.fees') ? '' : 'collapsed' }}"
           data-toggle="collapse"
           href="#advancedUI"
           role="button"
           aria-expanded="{{ request()->routeIs('admin.classes.*') || request()->routeIs('admin.class.fees') ? 'true' : 'false' }}">

          <i class="link-icon" data-feather="home"></i>
          <span class="link-title">Classes</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>

        <div class="collapse {{ request()->routeIs('admin.classes.*') || request()->routeIs('admin.class.fees') ? 'show' : '' }}" id="advancedUI">
          <ul class="nav sub-menu">

            <li class="nav-item">
              <a href="{{ route('admin.classes.index') }}"
                 class="nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                 Class List
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('admin.class.fees') }}"
                 class="nav-link {{ request()->routeIs('admin.class.fees') ? 'active' : '' }}">
                 Class Fee
              </a>
            </li>

          </ul>
        </div>
      </li>

    </ul>
  </div>
</nav>