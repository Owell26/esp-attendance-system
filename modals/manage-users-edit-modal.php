<!-- Modal -->
<?php  
echo "
<!-- Edit Modal -->
<div class='modal fade' id='editModal$card_uid' tabindex='-1' aria-labelledby='editModalLabel$card_uid' aria-hidden='true'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='editModalLabel$card_uid'>Edit User: $name</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
      </div>
      <div class='modal-body'>
        <form action='../controller/update/edit_user.php' method='POST'>
            <input type='hidden' name='card_uid' value='$card_uid'>
            <div class='mb-3'>
                <label for='editFirstname$card_uid' class='form-label'>First Name</label>
                <input type='text' class='form-control' id='editFirstname$card_uid' name='firstname' value='$firstname' required>
            </div>
            <div class='mb-3'>
                <label for='editMiddlename$card_uid' class='form-label'>Middle Name</label>
                <input type='text' class='form-control' id='editMiddlename$card_uid' name='middlename' value='$middlename' required>
            </div>
            <div class='mb-3'>
                <label for='editLastname$card_uid' class='form-label'>Last Name</label>
                <input type='text' class='form-control' id='editLastname$card_uid' name='lastname' value='$lastname' required>
            </div>
            <div class='mb-3'>
                <label for='editSuffix$card_uid' class='form-label'>Suffix (Optional)</label>
                <input type='text' class='form-control' id='editSuffix$card_uid' name='suffix' value='$suffix'>
            </div>
            <div class='mb-3'>
                <label for='editUserType$card_uid' class='form-label'>User Type</label>
                <select class='form-select' id='editUserType$card_uid' name='user_type' required>
                    <option value='Student' ".($user_type=='Student'?'selected':'').">Student</option>
                    <option value='Staff' ".($user_type=='Staff'?'selected':'').">Staff</option>
                    <option value='Teacher' ".($user_type=='Teacher'?'selected':'').">Teacher</option>
                </select>
            </div>
            <button type='submit' name='editUser' class='btn btn-success'>Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>  
";