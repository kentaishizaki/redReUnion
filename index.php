<?php session_start();
	// なりすまし防止用IDの発行
	$insertId = base64_encode ( openssl_random_pseudo_bytes ( 28 ) );
	setcookie ( 'insertId', $insertId );
	$_SESSION['insertId'] = $insertId;
?>


<!DOCTYPE html>

<html lang = "ja">

	<head>

		<link rel = "stylesheet" type = "text/css" href = "css/style.css">

		<script type="text/javascript" src="//typesquare.com/3/tsst/script/ja/typesquare.js?5d0c5d788f244c49a2e341b4ac1e02ec" charset="utf-8"></script>

		<title>REDチーム連携システム「ReUnion」</title>

		<meta name = "viewport" content = "width = device-width, initial-scale = 1.0, minimum-scale = 1.0">
	
	</head>

	<body>
		
		<div id = "container">

			<div id = "title">
				<img src = "images/red.png" alt = "RED_ReUnion">
			</div>

			<div id = "login">
				<form action = "home.php" method = "post">
					<p><input type = "text" name = "userId" placeholder = "ユーザID"></p>
					<p><input type = "password" name = "password" placeholder = "パスワード"></p>
					<p><input type = "submit" value = "認証"></p>
				</form>
			</div>

		</div>

	</body>

</html>
