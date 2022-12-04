<?php
session_start();
require_once '../controlers/view_usersControler.php';
require_once '../blades/header.php';
?>

<section class="view_users">
  <h3 class="view_users_title">Users - <?php echo isset($_SESSION['registered'][0]['total']) ? $_SESSION['registered'][0]['total'] : 0; ?></h3>
  <form action="<?php echo base_path . str_replace(".php", "", basename($_SERVER['PHP_SELF']));  ?>" method="get" class="search_users" id="search_users">
      <input type="search" name="search" class="sarch" placeholder="search people" id="search_user">
  </form>
</section>

<section class="view_users m-500">
  <div class="users_div">
    <?php echo $element->displayUsers($users_data); ?>
  </div>
</section>
<br><br>


<?php
echo $users->_PAGINATION;
echo "<br><br>";
require_once '../blades/footer.php';
?>