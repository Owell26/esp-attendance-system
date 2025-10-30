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
            <tbody>
             <?php
                // Query only assigned users (with matching card_uid in both tables)
                $sql = "
                    SELECT attendance.card_uid, MAX(attendance.scan_time) AS scan_time,
                           users.first_name, users.middle_name, users.last_name, users.suffix
                    FROM attendance
                    INNER JOIN users ON TRIM(attendance.card_uid) = TRIM(users.card_uid)
                    WHERE TRIM(attendance.device_id) = TRIM('$device_id')
                    GROUP BY attendance.card_uid, users.first_name, users.middle_name, users.last_name, users.suffix
                    ORDER BY scan_time DESC
                ";

                $sql_run = mysqli_query($conn, $sql);

                if ($sql_run && mysqli_num_rows($sql_run) > 0) {
                    while ($row = mysqli_fetch_assoc($sql_run)) {
                        $card_uid = htmlspecialchars($row['card_uid']);
                        $scan_time = htmlspecialchars($row['scan_time']);

                        // Format name properly (Lastname, Firstname Middlename Suffix)
                        $fullname = htmlspecialchars(
                            trim($row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['suffix'])
                        );

                        echo "
                        <tr>
                            <td>$card_uid</td>
                            <td>$fullname</td>
                            <td>$scan_time</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center text-muted'>No assigned attendance records found.</td></tr>";
                }
            ?>
            </tbody>
        </table>

    </main>
  </div>
</div>

<?php include '../includes/footbar.php'; ?>
<?php include '../includes/footer.php'; ?>

<script src="../assets/js/fetch_attendance.js"></script>
