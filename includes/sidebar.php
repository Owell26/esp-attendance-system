<?php
// Detect the current page
$current_page = basename($_SERVER['PHP_SELF']);

// Helper function to check if page is active
function isActive($page, $current_page) {
    return ($page == $current_page) ? 'bg-dark p-2 rounded-3' : '';
}

// Helper function to check if a dropdown should be shown
function isDropdownActive($pages, $current_page) {
    return in_array($current_page, $pages) ? 'show' : '';
}
?>

<!-- Sidebar (Desktop & Tablet) -->
<nav class="col-md-3 col-lg-2 d-none d-md-block bg-leafy-green text-cream sidebar vh-100 position-fixed top-0 start-0 pt-5 mt-4  shadow-lg">
  <div class="d-flex flex-column h-100 p-3">

    <!-- Header -->
    <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-cream text-decoration-none">
      <i class="fas fa-user-shield me-2"></i>
      <span class="fs-5 fw-bold">Admin Panel</span>
    </a>
    <hr>

    <!-- Menu -->
    <ul class="nav nav-pills flex-column mb-auto" id="sidebarMenu">

      <!-- Dashboard -->
      <li class="mb-1 <?php echo isActive('dashboard.php', $current_page); ?>">
        <a href="dashboard.php" class="d-flex align-items-center fs-6 text-decoration-none text-cream">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>

      <!-- User Management -->
      <?php $userPages = ['register_user.php','manage_users.php']; ?>
      <li class="mb-1">
        <a href="#usermenu" 
           data-bs-toggle="collapse"
           class="d-flex align-items-center fs-6 text-decoration-none text-cream p-2"
           aria-expanded="<?php echo in_array($current_page, $userPages) ? 'true' : 'false'; ?>">
          <i class="bi bi-people-fill me-2"></i> Users
        </a>
        <div class="collapse <?php echo isDropdownActive($userPages, $current_page); ?>" id="usermenu" data-bs-parent="#sidebarMenu">
          <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ms-4 mt-2">
            <li>
              <a href="register_user.php" class="d-block text-decoration-none text-cream p-1 mb-2 <?php echo isActive('register_user.php', $current_page); ?>">
                <i class="bi bi-person-plus me-2"></i> Register User
              </a>
            </li>
            <li>
              <a href="manage_users.php" class="d-block text-decoration-none text-cream p-1 mb-2 <?php echo isActive('manage_users.php', $current_page); ?>">
                <i class="bi bi-person-gear me-2"></i> Manage Users
              </a>
            </li>
          </ul>
        </div>
      </li>

      <!-- Attendance -->
      <?php $attPages = ['view_attendance.php','attendance_report.php','class_schedule.php']; ?>
      <li class="mb-1">
        <a href="#attmenu" 
           data-bs-toggle="collapse"
           class="d-flex align-items-center fs-6 text-decoration-none text-cream p-2"
           aria-expanded="<?php echo in_array($current_page, $attPages) ? 'true' : 'false'; ?>">
          <i class="bi bi-calendar-check me-2"></i> Attendance
        </a>
        <div class="collapse <?php echo isDropdownActive($attPages, $current_page); ?>" id="attmenu" data-bs-parent="#sidebarMenu">
          <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ms-4 mt-2">
            <li>
              <a href="view_attendance.php" class="d-block text-decoration-none text-cream p-1 mb-2 <?php echo isActive('view_attendance.php', $current_page); ?>">
                <i class="bi bi-eye me-2"></i> View Attendance
              </a>
            </li>
            <li>
              <a href="attendance_report.php" class="d-block text-decoration-none text-cream p-1 mb-2 <?php echo isActive('attendance_report.php', $current_page); ?>">
                <i class="bi bi-file-earmark-bar-graph me-2"></i>Attendance Report
              </a>
            </li>
            <li>
              <a href="class_schedule.php" class="d-block text-decoration-none text-cream p-1 mb-2 <?php echo isActive('class_schedule.php', $current_page); ?>">
                <i class="bi bi-file-earmark-bar-graph me-2"></i>Class Schedule
              </a>
            </li>
          </ul>
        </div>
      </li>

      <!-- Advanced -->
      <?php $advPages = ['dynamic_sessions.php','offline_mode.php','live_display.php','history.php']; ?>
      <li class="mb-1">
        <a href="#advmenu" 
           data-bs-toggle="collapse"
           class="d-flex align-items-center fs-6 text-decoration-none text-cream p-2"
           aria-expanded="<?php echo in_array($current_page, $advPages) ? 'true' : 'false'; ?>">
          <i class="bi bi-gear-fill me-2"></i> Settings
        </a>
        <div class="collapse <?php echo isDropdownActive($advPages, $current_page); ?>" id="advmenu" data-bs-parent="#sidebarMenu">
          <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ms-4 mt-2">
            <li>
              <a href="dynamic_sessions.php" class="d-block text-decoration-none text-cream p-1 mb-2 <?php echo isActive('dynamic_sessions.php', $current_page); ?>">
                <i class="bi bi-clock-history me-2"></i> Dynamic Sessions
              </a>
            </li>
            <li>
              <a href="offline_mode.php" class="d-block text-decoration-none text-cream p-1 mb-2 <?php echo isActive('offline_mode.php', $current_page); ?>">
                <i class="bi bi-wifi-off me-2"></i> Offline Handling
              </a>
            </li>
            <li>
              <a href="live_display.php" class="d-block text-decoration-none text-cream p-1 mb-2 <?php echo isActive('live_display.php', $current_page); ?>">
                <i class="bi bi-display me-2"></i> Live Display
              </a>
            </li>
            <li>
              <a href="history.php" class="d-block text-decoration-none text-cream p-1 mb-2 <?php echo isActive('history.php', $current_page); ?>">
                <i class="bi bi-journal-text me-2"></i> History / Search
              </a>
            </li>
          </ul>
        </div>
      </li>

    </ul>

    <!-- Account (Bottom) -->
    <li class="mb-1 mt-auto list-unstyled mb-5">
      <div class="collapse bg-dark p-2 mb-2 rounded-3" id="accountmenu" data-bs-parent="#sidebarMenu">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ms-4 mb-2">
          <li>
            <a href="settings.php" class="d-block text-decoration-none text-cream p-1 mb-2 mt-2 <?php echo isActive('settings.php', $current_page); ?>">
              <i class="bi bi-gear-fill me-2"></i> Settings
            </a>
          </li>
          <li>
            <a href="profile.php" class="d-block text-decoration-none text-cream p-1 mb-2 <?php echo isActive('profile.php', $current_page); ?>">
              <i class="bi bi-person-circle me-2"></i> Profile
            </a>
          </li>
          <li><hr class="dropdown-divider text-cream"></li>
          <li>
            <a href="../controller/logout.php" class="d-block text-decoration-none text-danger p-1">
              <i class="bi bi-box-arrow-right me-2"></i> Sign out
            </a>
          </li>
        </ul>
      </div>

      <!-- Account trigger -->
      <a href="#accountmenu" data-bs-toggle="collapse" 
         class="d-flex align-items-center fs-6 text-decoration-none text-cream p-2" 
         aria-expanded="false">
        <img src="admin_profile.png" alt="Profile" width="32" height="32" 
             class="rounded-circle me-2 border border-light">
        <strong>Admin</strong>
        <i class="bi bi-caret-up-fill ms-auto"></i>
      </a>
    </li>

  </div>
</nav>
