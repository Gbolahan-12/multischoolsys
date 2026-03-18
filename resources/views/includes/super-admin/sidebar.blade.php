<nav class="sidebar">
  <div class="sidebar-header">
    <a href="{{ route('superadmin.dashboard') }}" class="sidebar-brand"> <img
        src="{{ asset('admin-assets/images/logo/minilogo-removebg-preview.png') }}" width="110px" height="110px"
        alt=""> </a>

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
      <li class="nav-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
        <a href="{{ route('superadmin.dashboard') }}" class="nav-link">
          <i class="link-icon" data-feather="box"></i>
          <span class="link-title">Dashboard</span>
        </a>
      </li>

      <li class="nav-item nav-category">Web Apps</li>

      <!-- Manage School -->
      <li class="nav-item {{ request()->routeIs('superadmin.schools.*') ? 'active' : '' }}">
        <a href="{{ route('superadmin.schools.index') }}" class="nav-link">
          <i class="link-icon" data-feather="home"></i>
          <span class="link-title">Manage School</span>
        </a>
      </li>

      <!-- School Subscription -->
      <li class="nav-item {{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}">
        <a href="{{ route('superadmin.subscriptions.index') }}" class="nav-link">
          <i class="link-icon" data-feather="credit-card"></i>
          <span class="link-title">School Subscription</span>
        </a>
      </li>
    </ul>
  </div>
</nav>