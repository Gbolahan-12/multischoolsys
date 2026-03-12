<nav class="sidebar">
      <div class="sidebar-header">
        <a @if (auth()->user()->role === 'admin')
      href="{{ route('admin.dashboard') }}"
    @else
    href="{{ route('staff.dashboard') }}"  
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
          <li class="nav-item">
            <a href="{{ route('staff.dashboard') }}" class="nav-link">
              <i class="link-icon" data-feather="box"></i>
              <span class="link-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item nav-category">web apps</li>

          <li class="nav-item">
            <a href="{{ route('staff.results.index') }}" class="nav-link">
              <i class="link-icon" data-feather="upload"></i>
              <span class="link-title">Upload Result</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('staff.report-cards.index') }}" class="nav-link">
              <i class="link-icon" data-feather="calendar"></i>
              <span class="link-title">Report Card</span>
            </a>
          </li>
        </ul>
      </div>
    </nav>
    {{-- <nav class="settings-sidebar">
      <div class="sidebar-body">
        <a href="#" class="settings-sidebar-toggler">
          <i data-feather="settings"></i>
        </a>
        <h6 class="text-muted">Sidebar:</h6>
        <div class="form-group border-bottom">
          <div class="form-check form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" name="sidebarThemeSettings" id="sidebarLight" value="sidebar-light" checked>
              Light
            </label>
          </div>
          <div class="form-check form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" name="sidebarThemeSettings" id="sidebarDark" value="sidebar-dark">
              Dark
            </label>
          </div>
        </div>
        <div class="theme-wrapper">
          <h6 class="text-muted mb-2">Light Theme:</h6>
          <a class="theme-item active" href="../demo_1/dashboard-one.html">
            <img src="{{asset('admin-assets/images/screenshots/light.jpg')}}" alt="light theme">
          </a>
          <h6 class="text-muted mb-2">Dark Theme:</h6>
          <a class="theme-item" href="../demo_2/dashboard-one.html">
            <img src="{{ asset('admin-assets/images/screenshots/dark.jpg') }}" alt="light theme">
          </a>
        </div>
      </div>
    </nav> --}}