<!-- FOOTBAR (Mobile Only) -->
<nav class="navbar navbar-dark bg-leafy-green navbar-expand d-md-none shadow-lg p-0 mt-5">
  <ul class="navbar-nav nav-justified w-100">
    <!-- Dashboard -->
    <li class="nav-item">
      <a href="dashboard.php" class="nav-link text-center text-cream">
        <i class="bi bi-speedometer2 fs-5 mb-1"></i>
        <div class="small">Dashboard</div>
      </a>
    </li>

    <!-- Users -->
    <li class="nav-item">
      <a href="#" class="nav-link text-center text-cream" data-bs-toggle="offcanvas" data-bs-target="#offcanvasUsers">
        <i class="bi bi-people fs-5 mb-1"></i>
        <div class="small">Users</div>
      </a>
    </li>

    <!-- Attendance -->
    <li class="nav-item">
      <a href="#" class="nav-link text-center text-cream" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAttendance">
        <i class="bi bi-calendar-check fs-5 mb-1"></i>
        <div class="small">Attendance</div>
      </a>
    </li>

    <!-- Advanced -->
    <li class="nav-item">
      <a href="#" class="nav-link text-center text-cream" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAdvanced">
        <i class="bi bi-gear-fill fs-5 mb-1"></i>
        <div class="small">Advanced</div>
      </a>
    </li>
  </ul>
</nav>

<!-- OFFCANVAS: Users -->
<div class="offcanvas offcanvas-bottom bg-dark text-white" tabindex="-1" id="offcanvasUsers" aria-labelledby="offcanvasUsersLabel" style="height: 45%;">
  <div class="offcanvas-header py-2 border-bottom">
    <h6 class="offcanvas-title mb-0" id="offcanvasUsersLabel">
      <i class="bi bi-people-fill me-2"></i> User Management
    </h6>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  
  <div class="offcanvas-body small pt-2">
    <ul class="list-unstyled mt-2">
      <li class="mb-1">
        <a href="register_user.php" class="d-block text-decoration-none text-cream py-2">
          <i class="bi bi-person-plus me-2"></i> Register User
        </a>
      </li>
      <li class="mb-1">
        <a href="manage_users.php" class="d-block text-decoration-none text-cream py-2">
          <i class="bi bi-person-gear me-2"></i> Manage Users
        </a>
      </li>
    </ul>
  </div>
</div>

<!-- OFFCANVAS: Attendance (compact header + spaced list) -->
<div class="offcanvas offcanvas-bottom bg-dark text-white" tabindex="-1" id="offcanvasAttendance" aria-labelledby="offcanvasAttendanceLabel" style="height: 45%;">
  <div class="offcanvas-header py-2 border-bottom">
    <h6 class="offcanvas-title mb-0" id="offcanvasAttendanceLabel">
      <i class="bi bi-calendar-check me-2"></i> Attendance
    </h6>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body small pt-2">
    <ul class="list-unstyled mt-2">
      <li class="mb-1">
        <a href="view_attendance.php" class="d-block text-decoration-none text-cream py-2">
          <i class="bi bi-eye me-2"></i> View Attendance
        </a>
      </li>
      <li class="mb-1">
        <a href="attendance_report.php" class="d-block text-decoration-none text-cream py-2">
          <i class="bi bi-file-earmark-bar-graph me-2"></i> Reports
        </a>
      </li>
      <li>
        <a href="export_attendance.php" class="d-block text-decoration-none text-cream py-2">
          <i class="bi bi-file-earmark-arrow-down me-2"></i> Export (CSV/PDF)
        </a>
      </li>
    </ul>
  </div>
</div>

<!-- OFFCANVAS: Advanced (compact header + spaced list) -->
<div class="offcanvas offcanvas-bottom bg-dark text-white" tabindex="-1" id="offcanvasAdvanced" aria-labelledby="offcanvasAdvancedLabel" style="height: 50%;">
  <div class="offcanvas-header py-2 border-bottom">
    <h6 class="offcanvas-title mb-0" id="offcanvasAdvancedLabel">
      <i class="bi bi-gear-fill me-2"></i> Advanced
    </h6>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body small pt-2">
    <ul class="list-unstyled mt-2">
      <li class="mb-1">
        <a href="dynamic_sessions.php" class="d-block text-decoration-none text-cream py-2">
          <i class="bi bi-clock-history me-2"></i> Dynamic Sessions
        </a>
      </li>
      <li class="mb-1">
        <a href="offline_mode.php" class="d-block text-decoration-none text-cream py-2">
          <i class="bi bi-wifi-off me-2"></i> Offline Handling
        </a>
      </li>
      <li class="mb-1">
        <a href="live_display.php" class="d-block text-decoration-none text-cream py-2">
          <i class="bi bi-display me-2"></i> Live Display
        </a>
      </li>
      <li>
        <a href="history.php" class="d-block text-decoration-none text-cream py-2">
          <i class="bi bi-journal-text me-2"></i> History / Search
        </a>
      </li>
    </ul>
  </div>
</div>
