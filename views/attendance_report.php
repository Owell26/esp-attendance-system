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
        // Determine selected month and convert to month name
        $selected_month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
        $selected_month_name = date('F', mktime(0, 0, 0, $selected_month, 1));
        ?>

        <!-- Search by Date -->
        <div class="mb-3">
            <input type="text" id="dateSearch" class="form-control" 
                   placeholder="Search by date (e.g., <?php echo $selected_month_name; ?> 1, <?php echo date('Y'); ?>)">
        </div>

        <!-- DTR Filter Form -->
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

                <!-- Optional: Teacher & Subject Filter -->
                <div class="col-md-4">
                    <label for="teacher_subject" class="form-label">Filter by Teacher / Subject:</label>
                    <select name="teacher_subject" id="teacher_subject" class="form-select">
                        <option value="">-- All Teachers / Subjects --</option>
                        <?php
                        if(isset($_GET['year_section']) && $_GET['year_section'] != '') {
                            $year_section_filter = mysqli_real_escape_string($conn, $_GET['year_section']);
                            $ts_sql = "SELECT cs.teacher_id, cs.subject, u.first_name, u.last_name
                                       FROM class_schedule cs
                                       JOIN users u ON cs.teacher_id = u.id
                                       WHERE cs.year_section='$year_section_filter'
                                       GROUP BY cs.teacher_id, cs.subject
                                       ORDER BY u.last_name, cs.subject";
                            $ts_run = mysqli_query($conn, $ts_sql);
                            if($ts_run && mysqli_num_rows($ts_run) > 0){
                                while($ts = mysqli_fetch_assoc($ts_run)){
                                    $ts_val = $ts['teacher_id'].'_'.$ts['subject'];
                                    $ts_text = htmlspecialchars($ts['last_name'].', '.$ts['first_name'].' | '.$ts['subject']);
                                    $selected = (isset($_GET['teacher_subject']) && $_GET['teacher_subject']==$ts_val) ? 'selected' : '';
                                    echo "<option value='$ts_val' $selected>$ts_text</option>";
                                }
                            }
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
            $teacher_subject_filter = isset($_GET['teacher_subject']) ? $_GET['teacher_subject'] : '';

            // Parse teacher/subject filter
            $filter_teacher_id = 0;
            $filter_subject = '';
            if($teacher_subject_filter != ''){
                list($filter_teacher_id, $filter_subject) = explode('_', $teacher_subject_filter);
                $filter_teacher_id = intval($filter_teacher_id);
                $filter_subject = mysqli_real_escape_string($conn, $filter_subject);
            }

            // Get number of days in the selected month
            $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            // Get students in the section
            $students_sql = "SELECT id, card_uid, first_name, middle_name, last_name, suffix 
                             FROM users 
                             WHERE year_section='$year_section' AND user_type='Student'
                             ORDER BY last_name, first_name";
            $students_run = mysqli_query($conn, $students_sql);

            if($students_run && mysqli_num_rows($students_run) > 0){

                // Loop per day of the month
                for($d=1; $d<=$num_days; $d++){
                    $date_str = date('F j, Y', strtotime("$year-$month-$d"));  
                    $day_of_week = date('N', strtotime("$year-$month-$d")); // 1=Mon, 7=Sun
                    $day_name = date('l', strtotime("$year-$month-$d"));    // Monday, Tuesday, etc.


                    echo "<div class='dailyTable data-date='$date_str'>";
                    echo "<h5 class='mt-4'>$date_str ($day_name)</h5>";



                    // Get teachers for this section/day (filter if selected)
                    $teacher_sql = "SELECT cs.subject, u.first_name, u.last_name, cs.teacher_id
                                    FROM class_schedule cs
                                    JOIN users u ON cs.teacher_id = u.id
                                    WHERE cs.year_section='$year_section' AND cs.day_of_week='$day_of_week'";
                    if($filter_teacher_id && $filter_subject != ''){
                        $teacher_sql .= " AND cs.teacher_id='$filter_teacher_id' AND cs.subject='$filter_subject'";
                    }
                    $teacher_sql .= " ORDER BY u.last_name, cs.subject";
                    $teacher_run = mysqli_query($conn, $teacher_sql);

                    if($teacher_run && mysqli_num_rows($teacher_run) > 0){
                        echo "<div class='p-2 mb-2' style='background-color: #ffc107; color: #000; font-weight: bold;'>";
                        while($teacher = mysqli_fetch_assoc($teacher_run)){
                            $teacher_name = htmlspecialchars($teacher['last_name'] . ", " . $teacher['first_name']);
                            $subject = htmlspecialchars($teacher['subject']);
                            echo "Teacher: $teacher_name | Subject: $subject <br>";
                        }
                        echo "</div>";
                    }

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

                        // Get scan times for this student on this date (filter by teacher/subject if selected)
                        $scan_sql = "SELECT scan_time 
                                     FROM attendance 
                                     WHERE card_uid='$card_uid' 
                                     AND DATE(scan_time) = '$year-$month-$d'";
                        if($filter_teacher_id && $filter_subject != ''){
                            $scan_sql .= " AND EXISTS (
                                SELECT 1 
                                FROM class_schedule cs
                                WHERE cs.year_section='$year_section'
                                  AND cs.teacher_id='$filter_teacher_id'
                                  AND cs.subject='$filter_subject'
                                  AND cs.day_of_week='$day_of_week'
                            )";
                        }
                        $scan_sql .= " ORDER BY scan_time ASC";
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
                echo "<div class='alert alert-info mt-3'>No students found in section $year_section.</div>";
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
