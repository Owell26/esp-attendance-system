<?php 
include '../includes/header.php';
include '../includes/navbar.php';
include '../auth/authentication.php';
?>

<div class="container-fluid">
  <div class="row">
    <?php include '../includes/sidebar.php'; ?>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 offset-md-3 offset-lg-2 pt-5">
        <h1 class="mt-5 h2">Attendance Report</h1>
        <hr>

        <?php
        // Get logged-in device_id
        $device_id = $_SESSION['device_id'];

        // Determine selected month and convert to month name
        $selected_month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
        $selected_month_name = date('F', mktime(0, 0, 0, $selected_month, 1));
        ?>

        <!-- Search by Date -->
        <div class="mb-3">
            <input type="text" id="dateSearch" class="form-control" 
                   placeholder="Search by date (e.g., <?php echo $selected_month_name; ?> 1, <?php echo date('Y'); ?>)">
        </div>

        <!-- Filter Form -->
        <form method="GET" class="mb-4">
            <div class="row g-3 align-items-end">

                <!-- Year & Section -->
                <div class="col-md-3">
                    <label for="year_section" class="form-label">Select Year & Section:</label>
                    <select name="year_section" id="year_section" class="form-select" required>
                        <option value="">-- Choose Section --</option>
                        <?php
                        $section_sql = "SELECT DISTINCT year_section FROM users WHERE user_type='Student' ORDER BY year_section ASC";
                        $section_run = mysqli_query($conn, $section_sql);
                        if($section_run && mysqli_num_rows($section_run) > 0){
                            while($sec = mysqli_fetch_assoc($section_run)){
                                $sec_val = htmlspecialchars($sec['year_section']);
                                $selected = (isset($_GET['year_section']) && $_GET['year_section']==$sec_val) ? 'selected' : '';
                                echo "<option value='$sec_val' $selected>$sec_val</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- Month -->
                <div class="col-md-2">
                    <label for="month" class="form-label">Month:</label>
                    <select name="month" id="month" class="form-select" required>
                        <?php
                        for($m=1; $m<=12; $m++){
                            $month_val = str_pad($m,2,'0',STR_PAD_LEFT);
                            $month_name = date('F', mktime(0,0,0,$m,1));
                            $selected = ($selected_month == $m) ? 'selected' : '';
                            echo "<option value='$month_val' $selected>$month_name</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">View Attendance</button>
                </div>

            </div>
        </form>

        <?php
        if(isset($_GET['year_section']) && $_GET['year_section'] != '') {

            $year_section = mysqli_real_escape_string($conn, $_GET['year_section']);
            $month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
            $year = date('Y');
          
            // Get number of days in the selected month (works even without calendar extension)
            $num_days = date('t', strtotime("$year-$month-01"));

            // Get students in the section who have attendance records from this device
            $students_sql = "
                SELECT DISTINCT users.id, users.card_uid, users.first_name, users.middle_name, users.last_name, users.suffix
                FROM users
                INNER JOIN attendance ON users.card_uid = attendance.card_uid
                WHERE users.year_section = '$year_section'
                  AND users.user_type = 'Student'
                  AND attendance.device_id = '$device_id'
                  AND YEAR(attendance.scan_time) = $year
                  AND MONTH(attendance.scan_time) = $month
                ORDER BY users.last_name, users.first_name
            ";
            $students_run = mysqli_query($conn, $students_sql);

            if($students_run && mysqli_num_rows($students_run) > 0){

                // Loop per day of the month
                for($d=1; $d<=$num_days; $d++){
                    $date_str = date('F j, Y', strtotime("$year-$month-$d"));  
                    $day_name = date('l', strtotime("$year-$month-$d"));    

                    echo "<div class='dailyTable' data-date='$date_str'>";
                    echo "<h5 class='mt-4'>$date_str ($day_name)</h5>";

                    echo "<table class='table table-bordered table-striped'>
                            <thead class='table-dark'>
                                <tr>
                                    <th>Card UID</th>
                                    <th>Full Name</th>
                                    <th>In</th>
                                    <th>Out</th>
                                </tr>
                            </thead>
                            <tbody>";

                    // Loop through each student
                    mysqli_data_seek($students_run, 0);
                    while($stu = mysqli_fetch_assoc($students_run)){
                        $card_uid = htmlspecialchars($stu['card_uid']);
                        $fullname = htmlspecialchars($stu['last_name'] . ", " . $stu['first_name'] . 
                                     ($stu['middle_name'] ? " ".$stu['middle_name'] : "") . 
                                     ($stu['suffix'] ? " ".$stu['suffix'] : ""));

                        // Get scan times for this student on this date and device
                        $scan_sql = "
                            SELECT scan_time
                            FROM attendance
                            WHERE card_uid = '$card_uid'
                              AND device_id = '$device_id'
                              AND YEAR(scan_time) = $year
                              AND MONTH(scan_time) = $month
                              AND DAY(scan_time) = $d
                            ORDER BY scan_time ASC
                        ";
                        $scan_run = mysqli_query($conn, $scan_sql);

                        $scan_times = [];
                        if($scan_run && mysqli_num_rows($scan_run) > 0){
                            while($row = mysqli_fetch_assoc($scan_run)){
                                $scan_times[] = date('g:i A', strtotime($row['scan_time']));
                            }
                        }

                        $in_time = isset($scan_times[0]) ? $scan_times[0] : '';
                        $out_time = isset($scan_times[1]) ? $scan_times[1] : '';

                        echo "<tr>
                                <td>$card_uid</td>
                                <td>$fullname</td>
                                <td>$in_time</td>
                                <td>$out_time</td>
                              </tr>";
                    }

                    echo "</tbody></table></div>";
                }

            } else {
                echo "<div class='alert alert-info mt-3'>No attendance found in section $year_section for this month.</div>";
            }
        }
        ?>

    </main>
  </div>
</div>

<script>
// Dynamic search by date
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("dateSearch");
    if (!searchInput) return;

    searchInput.addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        const dailyTables = document.querySelectorAll(".dailyTable");

        dailyTables.forEach(div => {
            let date = div.getAttribute("data-date").toLowerCase();
            div.style.display = date.includes(filter) ? "" : "none";
        });
    });
});
</script>

<script src="../assets/js/printDtr.js"></script>

<?php include '../includes/footbar.php'; ?>
<?php include '../includes/footer.php'; ?>  
