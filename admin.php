<?php
include('php/moderator.php');
include('php/csrf.php');
$invalid = false;
if (! $moderators and ! $admins){
  echo 'You have not enabled moderators or admins. Do this by editing php/moderator.php and set at least one to true.';
  echo '<br><br>This page is disabled by default for security reasons.';
  die(0);
}
class MyDB extends SQLite3 {
   function __construct() {
      $this->open('php/threadList.db');
   }
}

if (isset($_POST['delete'])){
  if ($_POST['csrf'] != $_SESSION['CSRF']){
    die('Invalid csrf');
  }
  if ($_SESSION['mtStaff']){
    if ($_SESSION['mtStaff'] !== false){
      $post = $_POST['post'];
      unlink($post);
      $db = new MyDB();
      $postSQL = str_replace('.html', '', str_replace('posts/', '', $db->escapeString($post)));
      $sql =<<<EOF
         DELETE from Threads where TITLE = '$postSQL';
EOF;

      $ret = $db->exec($sql);

      die('deleted');
    }
  }
}

class StaffDB extends SQLite3 {
   function __construct() {
      $this->open('php/staff.db');
   }
}


if (! file_exists('php/staff.db')){
  touch("php/staff.db");
  class StaffDB extends SQLite3 {
     function __construct() {
        $this->open('php/staff.db');
     }
  }
  $db = new StaffDB();

  $sql =<<<EOF
     CREATE TABLE Staff
     (username text PRIMARY KEY NOT NULL,
      password text not null);
EOF;

  $ret = $db->exec($sql);
  if(!$ret){
     echo "Unable to create database: " . $db->lastErrorMsg();
  }
  $db->close();
}
if (! isset($_SESSION['mtStaff'])){
  $rank = false;
}
else{
  $rank = $_SESSION['mtStaff'];
}
if ($rank === false){
  if (isset($_POST['password'])){
    if ($_POST['csrf'] != $_SESSION['CSRF']){
      die('Invalid csrf');
    }
    if (isset($_POST['username'])){
      if ($_POST['username'] == ''){
        if ($_POST['password'] == $adminPW){
          $rank = 'admin';
          $_SESSION['mtStaff'] = $rank;
        }
      }
      else{
        $db = new MyDB();
        $username = $db->escapeString($_POST['username']);
        $sql =<<<EOF
           Select * From Staff where username='$username'
EOF;
        $ret = $db->query($sql);
        while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
          if (password_verify($_POST['password'], $row['PASSWORD'])){
            $rank = $_POST['username'];
            $_SESSION['mtStaff'] = $rank;
          }
          else{
            $invalid = true;
          }
        }
      }
    }
  }
}
?>
<!DOCTYPE HTML>
<html>
<head>
  <title>MicroTXT Admin Panel</title>
  <meta charset='utf-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <style>
  h1{
    font-family: sans-serif;
  }
  .center{
    text-align: center;
  }
  input{
    margin-top: 1em;
  }
  </style>
</head>
<body>
  <h1 class='center'>MicroTXT Admin Panel</h1>
  <?php
  if ($rank === false){
    // not staff
    if ($invalid){
      echo 'You fucked up!';
    }
    echo '<div class="center"><form method="post" action="admin.php"><label style="display: none;">Mod Username (blank for admins): <input type="text" name="username"></label>
    <br><label>Password: <input required type="password" name="password"></label><br><input type="submit" value="Login"><input type="hidden" name="csrf" value="' . $CSRF . '"></form></div>';
  }
  elseif ($rank !== false){
    // admin or moderator
    echo '<input type="hidden" id="CSRF" value="' . $_SESSION['CSRF'] . '">';
    echo '<noscript><h1 style="color: red;">JS is required, sorry</h1></noscript>';
    echo '<script src="admin.js"></script>';
    echo '<p>To delete a reply in a thread, be logged in on this page and visit the thread. A form to delete replys by ID should be present.';
    echo '<h2 class="center">Delete a thread</h2>';
    echo '<div class="center">';
    foreach (glob('posts/'. '{,.}*.html', GLOB_BRACE)  as $filename) {
        echo "<span id=\"$filename\">" . htmlspecialchars(str_replace('.html', '', str_replace('posts/', '', "$filename"))) . " <button onClick=\"deletePost('$filename')\">Delete</button><br><br></span>";
    }
    echo '</div>';
  }
  else{
    echo "you are just a moderator";
  }
  if ($rank == 'admin'){
    echo '';
  }
  ?>
</body>
