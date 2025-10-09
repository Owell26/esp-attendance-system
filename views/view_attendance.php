<?php 
include '../includes/header.php';
include '../includes/navbar.php';
include '../auth/authentication.php';
?>

<div class="container-fluid">
  <div class="row">
    <?php include '../includes/sidebar.php'; ?>
    <!-- Main content -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 offset-md-3 offset-lg-2 pt-5">
        <h1 class="mt-5 h2">View Attendance</h1>
        <hr>

        <div class="d-flex justify-content-end">
            <div class="col-12 col-md-6 mb-3 mt-3">
                <input type="text" id="userSearch" class="form-control" placeholder="Search by Card UID, Name, Scan Time, or Session Type">
            </div>
        </div>

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
    </main>
  </div>
</div>

<?php include '../includes/footbar.php'; ?>
<?php include '../includes/footer.php'; ?>

<script src="../assets/js/fetch_attendance.js"></script>
