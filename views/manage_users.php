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
        <div class="d-flex justify-content-between align-items-center mt-5 ">
            <h1 class="h3">Manage Users</h1>
            <div class="mb-1 mb-md-0">
                <?php
                // Count all users
                $count_sql = "SELECT COUNT(*) AS total FROM users";
                $count_run = mysqli_query($conn, $count_sql);
                $count_row = mysqli_fetch_assoc($count_run);
                $total_users = $count_row['total'] ?? 0;
                ?>
                <span class="badge bg-warning text-black fs-6">Total Users: <?= $total_users ?></span>
            </div>
        </div>
        <hr>

        <div class="d-flex justify-content-end">
            <div class="col-12 col-md-6 mb-3 mt-3">
                <input type="text" id="userSearch" class="form-control" placeholder="Search by Card UID, Scan Time, or Session Type">
            </div>
        </div>


        <table class="table table-bordered table-striped" id="attendanceTable">
            <thead class="table-dark">
                <tr>
                    <th onclick="sortTable(0)" style="cursor: pointer;">Card UID &#x25B2;&#x25BC;</th>
                    <th onclick="sortTable(1)" style="cursor: pointer;">First Name &#x25B2;&#x25BC;</th>
                    <th onclick="sortTable(2)" style="cursor: pointer;">Middle Name &#x25B2;&#x25BC;</th>
                    <th onclick="sortTable(3)" style="cursor: pointer;">Last Name &#x25B2;&#x25BC;</th>
                    <th onclick="sortTable(4)" style="cursor: pointer;">Suffix &#x25B2;&#x25BC;</th>
                    <th onclick="sortTable(5)" style="cursor: pointer;">User Type &#x25B2;&#x25BC;</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

           <?php
            // Fetch all users
            $sql = "
                SELECT DISTINCT users.*
                FROM users
                INNER JOIN attendance ON users.card_uid = attendance.card_uid
                WHERE TRIM(attendance.device_id) = TRIM('$device_id')
            ";
            $sql_run = mysqli_query($conn, $sql);

            if ($sql_run && mysqli_num_rows($sql_run) > 0) {

                while ($row = mysqli_fetch_assoc($sql_run)) {
                    $card_uid = htmlspecialchars($row['card_uid']);
                    $firstname = htmlspecialchars($row['first_name']);
                    $middlename = htmlspecialchars($row['middle_name']);
                    $lastname = htmlspecialchars($row['last_name']);
                    $suffix = htmlspecialchars($row['suffix']);

                    $name = "$lastname, $firstname $middlename $suffix";
                    $user_type = htmlspecialchars($row['user_type']);
                    
                    echo "<tr>
                            <td>$card_uid</td>
                            <td>$firstname</td>
                            <td>$middlename</td>
                            <td>$lastname</td>
                            <td>$suffix</td>
                            <td>$user_type</td>
                            <td>
                                <!-- View Button -->
                                <button type='button' class='btn btn-sm btn-info mb-1' data-bs-toggle='modal' data-bs-target='#viewModal$card_uid'>
                                    View
                                </button>

                                <!-- Edit Button -->
                                <button type='button' class='btn btn-sm btn-primary mb-1' data-bs-toggle='modal' data-bs-target='#editModal$card_uid'>
                                    Edit
                                </button>

                                <!-- Delete Button -->
                                <button type='button' class='btn btn-sm btn-danger mb-1' data-bs-toggle='modal' data-bs-target='#deleteModal$card_uid'>
                                    Delete
                                </button>
                                ";
                                include '../modals/manage-users-view-modal.php';
                                include '../modals/manage-users-edit-modal.php';
                                include '../modals/manage-users-delete-modal.php';
                                echo "
                            </td>
                          </tr>";
                }
            } else {
                echo "<p class='text-center'>No users found.</p>";
            }
            ?>
            </tbody>
        </table>

    </main>
  </div>
</div>

<?php include '../includes/footbar.php'; ?>
<?php include '../includes/footer.php'; ?>
