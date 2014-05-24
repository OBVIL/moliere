<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<h1>Mise à jour du site Molière</h1>
<?php
ini_set('max_execution_time', -1);
if(isset($_POST['update'])) {
  echo '<pre>';
  system(dirname(__FILE__).'/up.sh');
  echo '</pre>';
}
?>
<form method="post">
<button name="update" type="submit">Update</button>
</form>
<p>L’opération peut prendre plusieurs minutes</p>
