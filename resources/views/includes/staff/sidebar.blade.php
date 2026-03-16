<nav class="sidebar">
  <div class="sidebar-header">
    <a 
      @if (auth()->user()->role === 'admin')
        href="{{ route('admin.dashboard') }}"
      @else
        href="{{ route('staff.dashboard') }}"
      @endif 
      class="sidebar-brand">
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
      <li class="nav-item {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
        <a href="{{ route('staff.dashboard') }}" class="nav-link">
          <i class="link-icon" data-feather="box"></i>
          <span class="link-title">Dashboard</span>
        </a>
      </li>

      <li class="nav-item nav-category">Web Apps</li>

      <!-- Upload Result -->
      <li class="nav-item {{ request()->routeIs('staff.results.*') ? 'active' : '' }}">
        <a href="{{ route('staff.results.index') }}" class="nav-link">
          <i class="link-icon" data-feather="upload"></i>
          <span class="link-title">Upload Result</span>
        </a>
      </li>

      <!-- Report Card -->
      <li class="nav-item {{ request()->routeIs('staff.report-cards.*') ? 'active' : '' }}">
        <a href="{{ route('staff.report-cards.index') }}" class="nav-link">
          <i class="link-icon" data-feather="calendar"></i>
          <span class="link-title">Report Card</span>
        </a>
      </li>

    </ul>
  </div>
</nav>