<?php
require_once('Util.php');

/* Checking if the id is null or empty string. If it is not, it will update the note's coordinates. */
if (!(IsNullOrEmptyString($_POST['id']))) {
	require_once('Note.php');
	require_once('DAO.php');
	$dao = new DAO();
	$id = htmlentities($_POST['id']);
	$x = htmlentities($_POST['x']);
	$y = htmlentities($_POST['y']);
	$note = new Note($id, null, $x, $y);
	$dao->updateNote($note);
}
echo 0;