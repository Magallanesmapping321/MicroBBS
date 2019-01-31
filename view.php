<?php
/*
MicroTXT - A tiny PHP Textboard Software
Copyright (c) 2016 Kevin Froman (https://ChaosWebs.net/)

MIT License
*/
include('php/settings.php');
include('php/csrf.php');

if (! isset($_GET['post']))
{
	header('location: index.php');
	die(0);
}
$id = $_GET['post'];
$id = str_replace('/', '', $id);
$id = str_replace('\\', '', $id);

$id = htmlspecialchars($id);

$postFile = 'posts/' . $id . '.html';

if (! file_exists($postFile))
{
	http_response_code(404);
	header('location: 404.html');
	die(0);
}

$data = file_get_contents($postFile);
// DomDocument is stupid and likes to append tags automatically, & breaks without a doctype.
$data = str_replace('<!DOCTYPE HTML>', '', $data);
$data = str_replace('<body>', '', $data);
$data = str_replace('</body>', '', $data);
$data = str_replace('<html>', '', $data);
$data = str_replace('</html>', '', $data);

if (isset($_SESSION['mtStaff'])){
  $rank = $_SESSION['mtStaff'];
}
else{
  $rank = '';
}
// Check if they can moderate (both moderators and admins can do this)
if ($rank != '' and $rank !== false){
  $mod = true;
}
else{
  $mod = false;
}
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset='utf-8'>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<title><?php echo $siteTitle . ' - ' . htmlspecialchars($id);?></title>
	<link rel="icon" type="image/x-icon" href="favicon.png?v=1">
	<link rel='stylesheet' href='theme.css'>
</head>
<body>
	<h1 class='center logo'><a href='index.php'><?php echo $siteTitle; ?></a></h1>
	<?php
  if ($mod){
    echo '<h2>Delete Reply</h2><form method="post" action="deleter.php"><input type="hidden" name="CSRF" value=' . $CSRF . '><input name="replyID" type="text" placeholder="reply id"><input type="hidden" name="thread" value="' . $id . '"><br><br><br><input type="submit" value="Delete Reply"></form>';
  }
	echo $data;
	?>
	<h2 id='replyTitle'>Reply</h2>
	<form method='post' action='reply.php' id='reply'>
		<label>Name: <input required type='text' name='name' maxlength='20' value='Anonymous'></label>
		<br><br>
		<label>Tripcode: <input type='password' name='tripcode' maxlength='100'></label>
		<br><br>
		<label>ID of post to reply to: <input required name='replyTo' type='text' maxlength='30' id='replyTo'></label>
		<br><br>
				<?php
		/*
		<label><input name='sage' type='checkbox'> Sage (Don't bump)</label>
		<br><br>
		*/?>
		<textarea required name='text' maxlength='100000' placeholder='Text Post' cols='50' rows='10'></textarea>
		<br><br>
		<input type='hidden' name='CSRF' value='<?php echo $CSRF;?>'>
		<input type='hidden' name='threadID' value='<?php echo $id;?>'>
		<br>
		<?php
			if ($captcha)
			{
				if (! isset($_SESSION['currentPosts']))
				{
					$_SESSION['currentPosts'] = $postsBeforeCaptcha;
				}
				if ($_SESSION['currentPosts'] >= $postsBeforeCaptcha)
				{
					echo '<img src="php/captcha.php" alt="captcha">';
					echo '<br><br><label>Captcha Text: <input required type="text" name="captcha" maxlength="10"></label><br><br>';
				}
			}
		?>
		<input type='submit' value='Reply'>
	</form>
	<script src='view.js'></script>
	<?php
	if ($keepSessionAlive == true){
		echo '<iframe src="keep-alive.php" style="display: none;"></iframe>';
	}
	?>
</body>
</html>
