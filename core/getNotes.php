<?php

/* Checking to see if the user is logged in, and if they are, it is getting all of their notes from
the database. */
session_start();
session_regenerate_id(true);
if (isset($_SESSION['user_id'])) {
  require_once('DAO.php');
  $dao = new DAO();
  $id = $_SESSION['user_id'];
  echo $dao->getAllNotes($id);
}