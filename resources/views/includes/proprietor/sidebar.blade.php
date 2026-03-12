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
          <li class="nav-item">
            <a href="{{ route('proprietor.dashboard') }}" class="nav-link">
              <i class="link-icon" data-feather="box"></i>
              <span class="link-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item nav-category">web apps</li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#emails" role="button" aria-expanded="false" aria-controls="emails">
              <i class="link-icon" data-feather="calendar"></i>
              <span class="link-title">Session</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="emails">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="{{ route('proprietor.sessions.index') }}" class="nav-link">Session</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.students.import.form') }}" class="nav-link">Upload Students</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a href="{{ route('proprietor.users.index') }}" class="nav-link">
              <i class="link-icon" data-feather="users"></i>
              <span class="link-title">Staff</span>
            </a>
          </li>
          <li class="nav-item nav-category">Components</li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#uiComponents" role="button" aria-expanded="false" aria-controls="uiComponents">
              <i class="link-icon" data-feather="credit-card"></i>
              <span class="link-title">Payment</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="uiComponents">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="{{ route('admin.payments.create') }}" class="nav-link">Make Payment</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.paid-students.index') }}" class="nav-link">Completed</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.payments.defaulter.index') }}" class="nav-link">Optional</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.payments.index') }}" class="nav-link">All Payments</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#advancedUI" role="button" aria-expanded="false" aria-controls="advancedUI">
              <i class="link-icon" data-feather="home"></i>
              <span class="link-title">Classes</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="advancedUI">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="{{ route('admin.classes.index') }}" class="nav-link">Class List</a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.class.fees')}}" class="nav-link">Class Fee</a>
                  </li>
              </ul>
            </div>
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
            <img src="../assets/images/screenshots/light.jpg" alt="light theme">
          </a>
          <h6 class="text-muted mb-2">Dark Theme:</h6>
          <a class="theme-item" href="../demo_2/dashboard-one.html">
            <img src="../assets/images/screenshots/dark.jpg" alt="light theme">
          </a>
        </div>
      </div>
    </nav> --}}