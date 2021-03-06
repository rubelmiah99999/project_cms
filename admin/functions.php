<?php
$home_url = '/';

function confirm_query ($result) {

  global $connection; 

  if (!$result) {
    die ("QUERY FAILED" . mysqli_error ($connection));
  }
}

function redirect ($location) {

  header ("Location: ".$location);
  exit;
}

function insert_categories () {

  global $connection;

  if (isset ($_POST['submit'])) {
    $cat_title = $_POST['cat_title'];

    if ($cat_title == "" || empty ($cat_title)) {
      echo "This field should not be empty.";
    } else {
      $stmt = mysqli_prepare ($connection, 
        "INSERT INTO categories (cat_title) VALUES (?) ");

      mysqli_stmt_bind_param ($stmt, 's', $cat_title);
      mysqli_stmt_execute ($stmt);

      if (!$stmt) {
        die ("QUERY FAILED" . mysqli_error ($connection));
      }
    }
  }
}

function display_categories () {

  global $connection;

  $statement = mysqli_prepare ($connection, 
    "SELECT cat_id, cat_title FROM categories ");

  mysqli_stmt_execute ($statement);
  mysqli_stmt_bind_result ($statement, $cat_id, $cat_title); 

  while (mysqli_stmt_fetch ($statement)) {

      echo "<tr>";
      echo "<td><input class='checkboxes' type='checkbox' name='checkBoxArray[]' value='{$cat_id}'></td>";
      echo "<td>{$cat_id}</td>";
      echo "<td>{$cat_title}</td>";
      echo "<td><a href='categories.php?edit={$cat_id}'>Edit</a></td>";
      echo "<td><a data-toggle='modal' data-target='#delete{$cat_id}'>Delete</a></td>";
      echo "</tr>";

      delete_modal ($cat_id, 'category', 'categories.php');
  }
}

function delete_categories () {

  global $connection;

  // Get the cat_id for delete value, make delete query.
  if (isset ($_POST['delete'])) {
    $cat_id_for_delete = $_POST['id'];

    $stmt = mysqli_prepare ($connection,
      "DELETE FROM categories WHERE cat_id = ? ");
    mysqli_stmt_bind_param ($stmt, 'i', $cat_id_for_delete);
    mysqli_stmt_execute ($stmt);

    if (!$stmt) {
      die ("QUERY FAILED" . mysqli_error ($connection));
    }

    redirect ("categories.php");
  }
}

function update_categories () {
  
  if (isset ($_GET['edit'])) {  //<-- this value is from table
    $cat_id = preg_replace ('#[^0-9]#', '', $_GET['edit']);

    include "includes/update_categories.php";
  }
}

function permission_warning () {
  if (!isset ($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    die ("<div class='alert alert-danger'>
          I'm sorry but you may not have a right permission to see this page.
          <a class='alert-link' href='/admin'> Back to Home.</a></div>");
  }
}


function users_online () {

  if (isset ($_GET['usersonline'])) {

    global $connection;

    if (!$connection) {
      session_start ();
      include ('../includes/db.php');

    $session_id = session_id ();
    $time = time ();
    $timeout_in_seconds = 300;
    $timeout = $time - $timeout_in_seconds;

    // If this user's in the DB. 

    $query = "SELECT * FROM users_online WHERE session_id = ? ";
    $stmt  = $connection->prepare ($query);
    $stmt->bind_param ("s", $session_id);
    $stmt->execute ();
    $result = $stmt->get_result ();
    $user_count = $result->num_rows;

    // If this user's not in the DB, insert current users id and time.
    // and if this user's in the DB, just update the infomations.
    if ($user_count === 0) {
      $query = "INSERT INTO users_online (session_id, time) VALUES ( ?, ? )";
      $stmt  = $connection->prepare ($query);
      $stmt->bind_param ("ss", $session_id, $time);
      $stmt->execute ();
    } else {
      $query = "UPDATE users_online SET time = ? WHERE session_id = ? ";
      $stmt  = $connection->prepare ($query);
      $stmt->bind_param ("ss", $time, $session_id);
      $stmt->execute ();
    }

    // Now show all users online.
    $query    = "SELECT * FROM users_online WHERE `time` > ? ";
    //$query  = "SELECT * FROM users_online WHERE time > {$timeout} ";
    $stmt  = $connection->prepare ($query);
    $stmt->bind_param ("s", $timeout);
    $stmt->execute ();
    $result = $stmt->get_result ();
    $howmany_users = $result->num_rows;

    echo $howmany_users;
    }
  }
}
users_online ();

function delete_modal ($deleteId, $element, $address) {
    echo "  <!-- Modal for delete -->
            <form action='' method='post'>
              <div id='delete{$deleteId}' class='modal fade' tabindex='-1' role='dialog'>
                <div class='modal-dialog' role='document'>
                  <div class='modal-content'>
                    <div class='modal-header'>
                      <button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                      <h4 class='modal-title'>Delete {$element}</h4>
                    </div>
                    <div class='modal-body'>
                      <p>Are you sure to delete this {$element}?</p>
                    </div>
                    <div class='modal-footer'>";

                    //<a type='button' class='btn btn-primary' href='{$address}?delete={$deleteId}'>Delete</a>

      echo " <input type='hidden' name='id' value='{$deleteId}'>
                     <input class='btn btn-danger' type='submit' name='delete' value='Delete'>

                      <button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>
                    </div>
                  </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
             </div><!-- /.modal -->
            </form>";
}

// used 'index.php', displaying Cards
// Count the number of records from each table.
function recordCount ($tableName){
  
  global $connection;

  $stmt  = $connection->prepare ("SELECT * FROM {$tableName} ");
  $stmt->execute ();
  $res   = $stmt->get_result ();
  $result = $res->num_rows;

  return $result;
}


function recordCountOfUser ($tableName, $cond, $user){
  
  global $connection;

  $stmt  = $connection->prepare ("SELECT * FROM {$tableName} WHERE {$cond} = '{$user}' ");
  $stmt->execute ();
  $res   = $stmt->get_result ();
  $result = $res->num_rows;

  return $result;
}

function is_admin ($username) {
  
  global $connection;

  $query  = "SELECT user_role FROM users WHERE user_name = '$username' ";
  $result = mysqli_query ($connection, $query);
  confirm_query ($result);

  $row = mysqli_fetch_array ($result); 

  if ($row['user_role'] == 'Admin') {
    return true;
  } else {
    return false;
  }
}

function i_query ($query) {

  global $connection;

  return mysqli_query ($connection, $query);
}

function isLoggedIn () {

  if (isset($_SESSION['user_role'])) {
    return true;
  }

  return false;
}

function getLoggedInUserID () {
  if (isLoggedIn ()) {
    $username = $_SESSION['username'];

    $result = i_query ("SELECT * FROM users WHERE user_name = '{$username}'" );
    confirm_query ($result);

    $userResult = mysqli_fetch_array ($result);
    return (mysqli_num_rows ($result) >= 1) ? $userResult['user_id'] : false;
  }

  return false;
}



// used 'index.php', displaying Charts
// Count the number of records depending on each status from tables. 
function checkStatus ($tableName, $columnName, $status){

  global $connection;

  //$query = "SELECT * FROM ".$tableName." WHERE ".$columnName." = '{$status}' "; 
  //$select_query = mysqli_query ($connection, $query);
  //confirm_query ($select_query);
  //$result = mysqli_num_rows ($select_query);
  //
  $stmt = $connection->prepare ("SELECT * FROM {$tableName} WHERE {$columnName} = ? ");
  $stmt->bind_param ("s", $status);
  $stmt->execute ();
  $res   = $stmt->get_result ();
  $result = $res->num_rows;

  return $result;
}

?>
