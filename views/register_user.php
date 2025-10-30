<?php 
include '../includes/header.php';
include '../includes/navbar.php';
include '../auth/authentication.php';

if (isset($_POST['assign'])) {
    $card_uid = $_POST['card_uid'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $suffix = $_POST['suffix'];
    $user_type = $_POST['user_type'];
    $year_section = $_POST['year_section'];

    $sql = "INSERT INTO users (card_uid, first_name, middle_name, last_name, suffix, user_type, year_section) VALUES ('$card_uid', '$firstname', '$middlename', '$lastname', '$suffix', '$user_type', '$year_section')";
    if (mysqli_query($conn, $sql)) {
        header("Location: register_user.php?success=1");
        exit();
    } else {
        echo "<div class='alert alert-danger mt-3'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="container-fluid">
  <div class="row">
    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 offset-md-3 offset-lg-2 pt-5">
        <div class="d-flex justify-content-between align-items-center mt-5 ">
            <h1 class="h3">Register User</h1>
            <div class="mb-1 mb-md-0">
                <?php
                // Count unassigned
                $count_sql = "SELECT COUNT(*) AS total FROM attendance 
                              WHERE device_id = '$device_id' AND card_uid NOT IN (SELECT card_uid FROM users WHERE card_uid IS NOT NULL)";
                $count_run = mysqli_query($conn, $count_sql);
                $count_row = mysqli_fetch_assoc($count_run);
                $total_unassigned = $count_row['total'] ?? 0;
                ?>
                <span class="badge bg-warning text-dark fs-6">Unassigned Cards: <?= $total_unassigned ?></span>
            </div>
        </div>
        <hr>

        <div class="d-flex justify-content-end">
            <div class="col-12 col-md-6 mb-3 mt-3">
                <input type="text" id="userSearch" class="form-control" placeholder="Search by Card UID, Scan Time, or Session Type">
            </div>
        </div>


        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="attendanceTable">
                <thead class="table-dark">
                    <tr>
                        <th onclick="sortTable(0)" style="cursor: pointer;">Card UID &#x25B2;&#x25BC;</th>
                        <th onclick="sortTable(1)" style="cursor: pointer;">Full Name &#x25B2;&#x25BC;</th>
                        <th onclick="sortTable(2)" style="cursor: pointer;">Scan Time &#x25B2;&#x25BC;</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Main attendance query
                $sql = "SELECT card_uid, MAX(scan_time) AS scan_time
                        FROM attendance
                        WHERE device_id = '$device_id' AND card_uid NOT IN (SELECT card_uid FROM users WHERE card_uid IS NOT NULL)
                        GROUP BY card_uid
                        ORDER BY scan_time DESC";

                $sql_run = mysqli_query($conn, $sql);

                if ($sql_run && mysqli_num_rows($sql_run) > 0) {
                    while ($row = mysqli_fetch_assoc($sql_run)) {
                        $card_uid = htmlspecialchars($row['card_uid']);
                        $scan_time = htmlspecialchars($row['scan_time']);

                        echo "
                        <tr>
                            <td>$card_uid</td>
                            <td>Unassigned</td>
                            <td>$scan_time</td>
                            <td>
                                <button type='button' class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#assignModal$card_uid'>
                                    Assign User
                                </button>
                        ";
                        include '../modals/register-user-assign-modal.php';
                        echo "   
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center text-muted'>No unassigned Card UID records found.</td></tr>";
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
