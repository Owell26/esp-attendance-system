<?php 
include '../includes/header.php';
include '../includes/navbar.php';
include '../auth/authentication.php';

$totalUsersQuery = "SELECT COUNT(*) AS total_users 
                    FROM users 
                    INNER JOIN attendance ON users.card_uid = attendance.card_uid 
                    WHERE attendance.device_id = '$device_id'";
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['total_users'];

// Total Attendance (lahat ng records sa attendance table)
$totalAttendanceQuery = "
                    SELECT COUNT(attendance.id) AS total_attendance
                    FROM attendance
                    INNER JOIN users ON attendance.card_uid = users.card_uid
                    WHERE attendance.device_id = '$device_id'";
$totalAttendanceResult = mysqli_query($conn, $totalAttendanceQuery);
$totalAttendance = mysqli_fetch_assoc($totalAttendanceResult)['total_attendance'];
?>

<div class="container-fluid">
  <div class="row">
    <?php include '../includes/sidebar.php'; ?>
    <!-- Main content -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 offset-md-3 offset-lg-2 pt-5">
        <h1 class="mt-5 h2">Dashboard</h1>
        <hr>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-md-3">
                <a href="manage_users.php" class="text-decoration-none">
                    <div class="card text-white bg-primary shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Users</h6>
                                <h4 class="card-text"><?php echo $totalUsers; ?></h4>
                            </div>
                            <i class="bi bi-people-fill fs-2"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-md-3">
                <a href="view_attendance.php" class="text-decoration-none">
                    <div class="card text-white bg-success shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Attendance</h6>
                                <h4 class="card-text"><?php echo $totalAttendance; ?></h4>
                            </div>
                            <i class="bi bi-calendar-check fs-2"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Attendance Table -->
        <h5 class="mt-4">Recent Activity</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="attendanceTable">
                <thead class="table-dark">
                    <tr>
                        <th onclick="sortTable(0)" style="cursor: pointer;">Card UID &#x25B2;&#x25BC;</th>
                        <th onclick="sortTable(1)" style="cursor: pointer;">Name &#x25B2;&#x25BC;</th>
                        <th onclick="sortTable(2)" style="cursor: pointer;">Scan Time &#x25B2;&#x25BC;</th>
                    </tr>
                </thead>
                <tbody id="attendanceBody">
                    <!-- Attendance data will be loaded here -->
                </tbody>
            </table>
        </div>


        <style>
            .card:hover {
                transform: translateY(-5px);
            }
        </style>
    </main>

  </div>
</div>


<script src="../assets/js/fetch_attendance.js"></script>

<?php include '../includes/footbar.php'; ?>
<?php include '../includes/footer.php'; ?>
