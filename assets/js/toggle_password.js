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