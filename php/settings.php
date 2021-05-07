<?php
/*
MicroTXT - A tiny PHP Textboard Software
Copyright (c) 2016 Kevin Froman (https://ChaosWebs.net/)

MIT License
*/
session_start();

$mtVersion = '1.9.4';

/* BEGIN USER SET VARIABLES, MODIFING THIS PART IS SUPPORTED */

$siteTitle = 'MicroBBS Demo';
$motd = true;

$captcha = false;
$postsBeforeCaptcha = 3;

$reporting = false;

$allowHidden = true; // If you change this to false and there are already hidden posts, they won't be deleted

$salt = 'DEFAULT_SALT'; // IT IS VERY IMPORTANT FOR YOU TO UPDATE THIS TO SOMETHING LONG AND RANDOM

$threadListLimit = 10;

$keepSessionAlive = true; // Change this if you want people to be able to AFK for indefinite periods of time & not recieve CSRF or captcha errors

/* END USER SET VARIABLES */

if ($reporting)
{
	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
	error_reporting(-1);
}
?>
