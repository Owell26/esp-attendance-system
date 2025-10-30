<?php 
include 'includes/header.php';
include 'database/db_connect.php'; // <-- your DB connection, dapat $conn = mysqli_connect(...);

?>


<div class="container d-flex justify-content-center align-items-center" style="height:100vh">
    <div class="card p-5 rounded-4 bg-light text-center shadow-lg" style="max-width: 420px;">
        <div class="fs-1 mb-3">
            <i class="bi bi-person-circle"></i>
        </div>
        <h2>CTech SmartPAD</h2>
        <p>Admin Signup</p>

        <?php  
        if (isset($_SESSION['error'])) {
            $error = $_SESSION['error'];
            echo "<p class='text-danger'>$error</p>";
            unset($_SESSION['error']);
        }
        ?>

        <form method="POST" action="controller/signup.php">
            <div class="mb-3 input-group">
                <span class="input-group-text bg-primary text-white"><i class="bi bi-cpu-fill"></i></span>
                <input type="text" class="form-control" name="device_id" placeholder="Device ID" required style="box-shadow: none">
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text bg-primary text-white"><i class="bi bi-people"></i></span>
                <input type="text" class="form-control" name="full_name" placeholder="Full Name" required style="box-shadow: none">
            </div>

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

            <button type="submit" class="btn btn-primary w-100 mt-3">Signup</button>
        </form>

        <p class="mt-3">Already Have an Account? <a href="index.php">Login here</a></p>

        <div class="mt-2 text-muted fs-6">
            &copy; <?php echo date("Y"); ?> CTech SmartPAD. All rights reserved.
        </div>
    </div>

</div>

<script>
    function togglePassword() {
        let pwd = document.getElementById('password');
        let eye = document.getElementById('eyeIcon');
        if(pwd.type === "password") {
            pwd.type = "text";
            eye.classList.replace('bi-eye-fill','bi-eye-slash-fill');
        } else {
            pwd.type = "password";
            eye.classList.replace('bi-eye-slash-fill','bi-eye-fill');
        }
    }
</script>

<?php include 'includes/footer.php'; ?>

