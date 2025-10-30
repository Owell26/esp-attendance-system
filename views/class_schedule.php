<?php
include '../includes/header.php';
include '../includes/navbar.php';
include '../auth/authentication.php';

// Handle form submission
if (isset($_POST['add_schedule'])) {
    $year_section = mysqli_real_escape_string($conn, $_POST['year_section']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $class_time = mysqli_real_escape_string($conn, $_POST['class_time']);
    $day_of_week = intval($_POST['day_of_week']); // 1=Monday, 7=Sunday

    // Insert with device_id (no teacher_id)
    $sql = "INSERT INTO class_schedule (year_section, subject, device_id, class_time, day_of_week)
            VALUES ('$year_section', '$subject', '$device_id', '$class_time', $day_of_week)";
    
    if (mysqli_query($conn, $sql)) {
        $success_msg = "Class schedule added successfully!";
    } else {
        $error_msg = "Error: " . mysqli_error($conn);
    }
}
?>

<div class="container-fluid">
  <div class="row">
    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 offset-md-3 offset-lg-2 pt-5">
        <h1 class="h3 mt-5">Add Class Schedule</h1>
        <hr>

        <?php if (!empty($success_msg)) echo "<div class='alert alert-success'>$success_msg</div>"; ?>
        <?php if (!empty($error_msg)) echo "<div class='alert alert-danger'>$error_msg</div>"; ?>

        <form method="POST" class="mb-4">
            <div class="row g-3">

                <!-- Year & Section -->
                <div class="col-md-3">
                    <label for="year_section" class="form-label">Year & Section:</label>
                    <input type="text" name="year_section" id="year_section" class="form-control" placeholder="e.g., 1-A" required>
                </div>

                <!-- Subject -->
                <div class="col-md-3">
                    <label for="subject" class="form-label">Subject:</label>
                    <input type="text" name="subject" id="subject" class="form-control" placeholder="e.g., Math101" required>
                </div>

                <!-- Class Time -->
                <div class="col-md-3">
                    <label for="class_time" class="form-label">Class Time:</label>
                    <input type="text" name="class_time" id="class_time" class="form-control" placeholder="e.g., 8:00 am - 9:00 am" required>
                </div>

                <!-- Day of Week -->
                <div class="col-md-3">
                    <label for="day_of_week" class="form-label">Day of Week:</label>
                    <select name="day_of_week" id="day_of_week" class="form-select" required>
                        <option value="1">Monday</option>
                        <option value="2">Tuesday</option>
                        <option value="3">Wednesday</option>
                        <option value="4">Thursday</option>
                        <option value="5">Friday</option>
                        <option value="6">Saturday</option>
                        <option value="7">Sunday</option>
                    </select>
                </div>

            </div>

            <div class="mt-3">
                <button type="submit" name="add_schedule" class="btn btn-primary">Add Schedule</button>
            </div>
        </form>

        <hr>
        <h4>Existing Class Schedules (Your Device)</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Year & Section</th>
                        <th>Subject</th>
                        <th>Class Time</th>
                        <th>Day</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Only show schedules created by this device
                    $sched_sql = "SELECT * 
                                  FROM class_schedule
                                  WHERE device_id = '$device_id'
                                  ORDER BY year_section, day_of_week, class_time";
                    $sched_run = mysqli_query($conn, $sched_sql);

                    if ($sched_run && mysqli_num_rows($sched_run) > 0) {
                        $day_names = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                        while ($row = mysqli_fetch_assoc($sched_run)) {
                            $day_name = $day_names[$row['day_of_week'] - 1];
                            echo "<tr>
                                    <td>{$row['year_section']}</td>
                                    <td>{$row['subject']}</td>
                                    <td>{$row['class_time']}</td>
                                    <td>$day_name</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center text-muted'>No class schedules found for this device.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </main>
  </div>
</div>

<?php include '../includes/footbar.php'; ?>
<?php include '../includes/footer.php'; ?>
