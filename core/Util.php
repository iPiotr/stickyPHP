<?php

/**
 * If the variable is not set or is empty, return true
 */
function IsNullOrEmptyString($s)
{
	return (!isset($s) || trim($s) === '');
}

/**
 * It takes a string, hashes it, and returns the hashed string.
 */
function getHashedString($pString)
{
	return password_hash($pString, PASSWORD_DEFAULT);
}