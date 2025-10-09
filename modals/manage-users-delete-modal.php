<!-- Modal -->
<?php  
echo "
<!-- Delete Modal -->
<div class='modal fade' id='deleteModal$card_uid' tabindex='-1' aria-labelledby='deleteModalLabel$card_uid' aria-hidden='true'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='deleteModalLabel$card_uid'>Delete User: $name</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
      </div>
      <div class='modal-body'>
        <p>Are you sure you want to delete this user?</p>
      </div>
      <div class='modal-footer'>
        <form action='../controller/delete/delete_user.php' method='POST'>
            <input type='hidden' name='card_uid' value='$card_uid'>
            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
            <button type='submit' name='deleteUser' class='btn btn-danger'>Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

";