<?php
session_start();
$insertId = @$_COOKIE['insertId'];
if ( empty ( $insertId ) || $insertId != @$_SESSION['insertId'] ) {
	echo '<h1>エラー</h1>';
	echo '<p>セッションの不正<p>';
	die ();
}
?>


<!DOCTYPE html>
<html lang = "ja">

	<head>

		<link rel = "stylesheet" type = "text/css" href = "css/style.css">

		<script type="text/javascript" src="//typesquare.com/3/tsst/script/ja/typesquare.js?5d0c5d788f244c49a2e341b4ac1e02ec" charset="utf-8"></script>

		<title>管理者へのメッセージ | REDチーム連携システム「ReUnion」</title>

	</head>

	<body>


        <?php
        
		try {

			// データベース(MySQL)へ接続
			$db = new PDO ( "mysql:dbname=table_name;host=db_host;", "user", "password" );
			// 日時の取得
			$insert_date = date('Y-m-d H:i:s');
			// SQL文のプリコンパイル
			$data = $db -> prepare ( "select name, mailAddress from userInfo where userId = ?" );
			// プレースホルダに値を代入
			$data -> bindValue ( 1, htmlspecialchars ( $_POST['userId'] ) );
			// Postメソッドで受け取ったデータをデータベースへ追加
			$data -> execute ();
			// 取得したデータを変数に保管（ユーザIDが存在しない場合はnull）
			$userInfo = $data -> fetch ();

			// メンバー登録メールの生成（fmlメーリングリスト）
			$to = $userInfo['mailAddress'];
			$subject = "RED2019 ReUnion お問い合わせメール";
			$message = "<!DOCTYPE html><html><body><p>".$userInfo['name']."様</p><br><br><p>以下のとおり、お問い合わせを受け付けました。できるだけ早く返信させていただきます。</p><br><p>お問い合わせ内容</p><p>ご氏名：".$userInfo['name']."様</p><p>本文：".htmlspecialchars ( $_POST["message"] )."</p></body></html>";
			$headers = "From: mail@red2019april.com";
			$headers .= "\r\n";
			$headers .= "Cc: mail@red2019april.com";
			$headers .= "\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8";
			// メンバー登録メールの送信
			mail ( $to, $subject, $message, $headers );

			echo '
				<h2>お問い合わせを受け付けました。</h2>

				<p>'.$userInfo["name"].'様のご登録メールアドレス宛に、確認メールを送信いたしました。</p>
				<p>万一、登録されたメールアドレスに確認メールが届かない場合は、管理者：石﨑健太(<a href = "mailto:mail@red2019april.com">mail@red2019april.com</a>)までお問い合わせください。</p>

				<div>
					<form action = "home.php" method = "post">
						<input type = "hidden" name = "userId" value = "'.$_SESSION['userId'].'">
						<input type = "hidden" name = "password" value = "'.$_SESSION['password'].'">
						<input type = "submit" value = "ホーム画面">
					</form>
				</div>
			';

		}
		catch ( PDOException $e ) {

			// エラー内容の表示
			echo $e -> getMessage ();

			// 処理の終了
			die ();

		}
		finally {

			$db = null;   // データベース接続の切断

		}

        ?>
        
        
    </body>

</html>