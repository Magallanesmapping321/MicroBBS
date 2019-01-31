<?php
/*
MicroTXT - A tiny PHP Textboard Software
Copyright (c) 2016 Kevin Froman (https://ChaosWebs.net/)

MIT License
*/
include('php/settings.php');
include('php/csrf.php');

function tripcode($tripcode, $salt) {
	if ($tripcode == '')
	{
		return '';
	}
	else
	{
		return hash('sha256', $tripcode . $salt);
	}
}


// Redirect if some BS is going on
function redirectError($msg) {

  $domain = $_SERVER['HTTP_HOST'];

  $path = $_SERVER['SCRIPT_NAME'];

  $queryString = $_SERVER['QUERY_STRING'];
	$_SESSION['mtPostError'] = true;
	$_SESSION['mtPostErrorTxt'] = $msg;
	header('location: index.php');
	die(0);
}

if (! isset($_POST['text']) || ! isset($_POST['CSRF']) || ! isset($_POST['name']) || ! isset($_POST['threadID']) || ! isset($_POST['replyTo']) || ! isset($_POST['tripcode']))
{
	redirectError('Missing argument');
}

if ($_POST['CSRF'] != $_SESSION['CSRF'])
{
	redirectError('Invalid CSRF token');
}


if ($captcha)
{
	if (! isset($_SESSION['currentPosts']))
	{
		$_SESSION['currentPosts'] = $postsBeforeCaptcha;
	}
	if ($_SESSION['currentPosts'] >= $postsBeforeCaptcha)
	{
		if (! isset($_POST['captcha']))
		{
			redirectError('Invalid captcha');
		}
		if ($_POST['captcha'] != $_SESSION['captchaVal'])
		{
			redirectError('Invalid captcha');
		}
		else
		{
			$_SESSION['currentPosts'] = 0;
		}
	}
}

$replyTo = $_POST['replyTo'];

if (! is_numeric($replyTo)){
	redirectError('No reply ID set');
}

if ($replyTo == '')
{
	redirectError('No reply ID set');
}

$threadID = str_replace('\\', '', str_replace('/', '', $_POST['threadID']));

$threadFile = 'posts/' . $threadID . '.html';

if (! file_exists($threadFile))
{
	die('The thread you are replying in does not exist');
}


// Get user data

$text = $_POST['text'];
$name = $_POST['name'];
$tripcode = $_POST['tripcode'];

// html encode user data to prevent xss
$text = htmlspecialchars($text);
$name = htmlspecialchars($name);
if (strlen($_POST['text']) > 100000 || strlen($_POST['name'] > 20) || strlen($_POST['tripcode']) > 100) {
	redirectError('Text, name, or tripcode is too long.');
}
elseif (strlen($_POST['text']) == 0){
  redirectError('Reply text cannot be blank');
}


// Generate Post ID
$postID = time();

$doc = new DOMDocument;

$doc->loadHTML(mb_convert_encoding(file_get_contents($threadFile), 'HTML-ENTITIES', 'UTF-8'));

$parent = $doc->getElementById($replyTo);

if ($parent == null)
{
	die('Post id not found');
}

$child = $doc->createElement('div', $name);
$child->setAttribute( 'class', 'name');

$parent->appendChild( $child);

$tripcode = tripcode($tripcode, $salt);

if ($tripcode != '')
{
	$child = $doc->createElement('input');
	$child->setAttribute( 'class', 'tripcode');
	$child->setAttribute( 'type', 'text');
	$child->setAttribute( 'value', $tripcode);
	$child->setAttribute( 'readonly', 'readonly');

	$parent->appendChild($child);
}

$parent->appendChild( $child);

$child = $doc->createElement('div', $postID);
$child->setAttribute( 'class', 'postID');
$child->setAttribute( 'onClick', 'javascript: clickItem(\'' . $postID . '\');');

$parent->appendChild( $child);

$child = $doc->createElement('div', '>> ' . $replyTo);
$child->setAttribute( 'class', 'replyTo');
$child->setAttribute( 'onClick', 'javascript: clickItem(\'' . $replyTo . '\');');

$parent->appendChild( $child);

$child = $doc->createElement('div', $text);
$child->setAttribute( 'class', 'post');
$child->setAttribute( 'id', 'cont-' . $postID);

$parent->appendChild( $child);

$child = $doc->createElement('div');
$child->setAttribute( 'id', $postID);
$child->setAttribute( 'class', 'hiddenPostID');

$parent->appendChild( $child);

// Write html to thread file

file_put_contents($threadFile, nl2br($doc->saveHTML(), false));

// If captcha is to be used, increment the user session post count
if ($captcha)
{
	$_SESSION['currentPosts'] = $_SESSION['currentPosts'] + 1;
}


header('location: view.php?post=' . $threadID);

?>
