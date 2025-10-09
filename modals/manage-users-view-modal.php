<!-- Modal -->
<?php  
echo "
<!-- View Modal -->
<div class='modal fade' id='viewModal$card_uid' tabindex='-1' aria-labelledby='viewModalLabel$card_uid' aria-hidden='true'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='viewModalLabel$card_uid'>User Details: $name</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
      </div>
      <div class='modal-body'>
        <p><strong>Card UID:</strong> $card_uid</p>
        <p><strong>Name:</strong> $name</p>
        <p><strong>User Type:</strong> $user_type</p>
      </div>
    </div>
  </div>
</div>
";