<?php
require_once('Util.php');

/* This is a PHP script that is called from the ajax call in the javascript. It is checking to see if
the id is null or empty. If it is not, it will require the DAO.php file, create a new DAO object,
and then call the deleteNote function. */
if (!(IsNullOrEmptyString($_POST['id']))) {
	require_once('DAO.php');
	$dao = new DAO();
	$id = htmlentities($_POST['id']);
	$id = $dao->deleteNote($id);
}
echo 0;