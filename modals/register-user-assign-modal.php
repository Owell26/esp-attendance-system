<!-- Modal -->
<?php  
echo "
<div class='modal fade' id='assignModal$card_uid' tabindex='-1' aria-labelledby='assignModalLabel$card_uid' aria-hidden='true'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='assignModalLabel$card_uid'>Assign User for Card UID: $card_uid</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
      </div>
      <div class='modal-body'>
        <form method='POST'> 
            <input type='hidden' name='card_uid' value='$card_uid'>

            <div class='mb-3'>
                <label for='firstname$card_uid' class='form-label'>First Name</label>
                <input type='text' class='form-control' id='firstname$card_uid' name='firstname' required>
            </div>
            <div class='mb-3'>
                <label for='middlename$card_uid' class='form-label'>Middle Name</label>
                <input type='text' class='form-control' id='middlename$card_uid' name='middlename' required>
            </div>
            <div class='mb-3'>
                <label for='lastname$card_uid' class='form-label'>Last Name</label>
                <input type='text' class='form-control' id='lastname$card_uid' name='lastname' required>
            </div>
            <div class='mb-3'>
                <label for='suffix$card_uid' class='form-label'>Suffix (Optional)</label>
                <input type='text' class='form-control' id='suffix$card_uid' name='suffix'>
            </div>

            <div class='mb-3'>
                <label for='usertype$card_uid' class='form-label'>User Type</label>
                <select class='form-select' id='usertype$card_uid' name='user_type' required>
                    <option value='Staff'>Staff</option>
                    <option value='Teacher'>Teacher</option>
                    <option value='Student'>Student</option>
                </select>
            </div>

            <!-- Year & Section (hidden by default) -->
            <div class='mb-3' id='yearSectionDiv$card_uid' style='display:none;'>
                <label for='year_section$card_uid' class='form-label'>Year & Section</label>
                <input type='text' class='form-control' id='year_section$card_uid' name='year_section' placeholder='e.g., 1-A'>
            </div>

            <button type='submit' name='assign' class='btn btn-success'>Assign</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('usertype$card_uid').addEventListener('change', function() {
    var yearSectionDiv = document.getElementById('yearSectionDiv$card_uid');
    if(this.value === 'Student') {
        yearSectionDiv.style.display = 'block';
        document.getElementById('year_section$card_uid').required = true;
    } else {
        yearSectionDiv.style.display = 'none';
        document.getElementById('year_section$card_uid').required = false;
    }
});
</script>

";
?>