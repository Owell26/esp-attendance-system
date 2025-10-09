<?php 
include 'includes/header.php';
include 'database/db_connect.php'; // <-- your DB connection, dapat $conn = mysqli_connect(...);

// ✅ Check if there is any admin in the database
$checkAdmin = mysqli_query($conn, "SELECT COUNT(*) as total FROM admin");
$row = mysqli_fetch_assoc($checkAdmin);

if ($row['total'] == 0) {
    // No admin yet, insert default
    $full_name = "Owell Venne Kim Seguban";
    $username  = "Owellkim26";
    $password  = "Admin123"; // plain text
    $is_active = 1;

    $sql = "INSERT INTO admin (full_name, user_name, password, is_active) 
            VALUES ('$full_name', '$username', '$password', '$is_active')";
    mysqli_query($conn, $sql);
}
?>


<div class="container d-flex justify-content-center align-items-center" style="height:100vh">
    <div class="card p-5 rounded-4 bg-light text-center shadow-lg" style="max-width: 420px;">
        <div class="fs-1 mb-3">
            <i class="bi bi-person-circle"></i>
        </div>
        <h2>Ctech Attendance</h2>
        <p>Admin Login Portal</p>

        <?php  
        if (isset($_SESSION['error'])) {
            $error = $_SESSION['error'];
            echo "<p class='text-danger'>$error</p>";
            unset($_SESSION['error']);
        }
        ?>

        <form method="POST" action="controller/login.php">
            <div class="mb-3 input-group">
                <span class="input-group-text bg-primary text-white"><i class="bi bi-person-fill"></i></span>
                <input type="text" class="form-control" name="username" placeholder="Username" required style="box-shadow: none">
            </div>

            <div class="mb-1 input-group">
                <span class="input-group-text bg-primary text-white"><i class="bi bi-lock-fill"></i></span>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required style="box-shadow: none">
                <span class="input-group-text bg-primary text-white show-password" onclick="togglePassword()" style="cursor:pointer">
                    <i class="bi bi-eye-fill" id="eyeIcon"></i>
                </span>
            </div>
            <div class="d-flex justify-content-end mt-2">
                <a href="#" class="text-end">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
        </form>

        <div class="mt-3 text-muted fs-6">
            &copy; <?php echo date("Y"); ?> Ctech Attendance System. All rights reserved.
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>

