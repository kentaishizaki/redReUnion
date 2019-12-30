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

		<title>メーリングリスト新規登録 | REDチーム連携システム「ReUnion」</title>

	</head>

	<body>


        <?php
        
		try {

			// データベース(MySQL)へ接続
			$db = new PDO ( "mysql:dbname=table_name;host=db_host;","user","password");

			// SQL文のプリコンパイル（重複するユーザデータの有無を検証）
			$data = $db -> prepare ( "select * from userInfo where userId = ?; ");
			// プレースホルダに値を代入
			$data -> bindValue ( 1, htmlspecialchars ( $_POST['newUserId'] ) );
			// Postメソッドで受け取ったデータをデータベースへ追加
			$data -> execute ();

			// 重複するユーザデータがないことを検証
			if ( $userId = $data -> fetch () ) {

				echo '
					<h2>エラー（登録に失敗しました）</h2>

					<p>入力されたユーザIDは、すでに使用されております。</p>
					<p>ほかのユーザIDを入力してください。</p>

					<div>
						<form action = "home.php" method = "post">
							<input type = "hidden" name = "userId" value = "Sanbancho1-1">
							<input type = "hidden" name = "password" value = "Sanbancho1-1">
							<input type = "hidden" name = "userName" value = "'.htmlspecialchars ( $_POST['userName'] ).'">
							<input type = "hidden" name = "userMailAddress" value = "'.htmlspecialchars ( $_POST['userMailAddress'] ).'">
							<input type = "hidden" name = "newUserId" value = "'.htmlspecialchars ( $_POST['newUserId'] ).'">
							<input type = "submit" value = "もどる">
						</form>
					</div>
				';

			}
			else {

				// 日時の取得
				$insert_date = date('Y-m-d H:i:s');
				// SQL文のプリコンパイル
				$data = $db -> prepare ( "insert into userInfo ( insertDate, name, userId, password, mailAddress ) values ( ?, ?, ?, ?, ? ); ");
				// プレースホルダに値を代入
				$data -> bindValue ( 1, $insert_date );
				$data -> bindValue ( 2, htmlspecialchars ( $_POST['userName'] ) );
				$data -> bindValue ( 3, htmlspecialchars ( $_POST['newUserId'] ) );
				$data -> bindValue ( 4, password_hash ( htmlspecialchars ( $_POST['newPassword'] ), PASSWORD_BCRYPT ) );
				$data -> bindValue ( 5, htmlspecialchars ( $_POST['userMailAddress'] ) );
				// Postメソッドで受け取ったデータをデータベースへ追加
				$data -> execute ();

				// メンバー登録メールの生成（fmlメーリングリスト）
				$to1 = "ml-ctl@red2019april.com";
				$subject1 = "Add Member";
				$message1 = "#admin pass password\n#admin add ".htmlspecialchars ( $_POST["userMailAddress"] );   // 稼働時はpasswordをパスワードに置換
				$headers1 = "From: mail@red2019april.com";
				$headers1 .= "\r\n";
				$headers1 .= "Content-type: text/html; charset=UTF-8";
				// メンバー登録メールの送信
				#mail($to1, $subject1, $message1, $headers1);
				// メーリングリスト休止中

				// メンバー登録メールの生成（fmlメーリングリスト）
				$to2 = htmlspecialchars ( $_POST['userMailAddress'] );
				$subject2 = "ようこそ！REDチーム・メーリングリストへ";
				$message2 = "<!DOCTYPE html><html><body><p>".htmlspecialchars ( $_POST["userName"] )."様</p><p>REDチーム連携システム「ReUnion」への登録が完了しました。</p><p>ご不明な点は、管理者(<a href = 'mailto:mail@red2019april.com'>mail@red2019april.com</a>)までお問い合わせください。</p></body></html>";
				$headers2 = "From: mail@red2019april.com";
				$headers2 .= "\r\n";
				$headers2 .= "Cc: mail@red2019april.com";
				$headers2 .= "\r\n";
				$headers2 .= "Content-type: text/html; charset=UTF-8";
				// メンバー登録メールの送信
				mail($to2, $subject2, $message2, $headers2);

				echo '
					<h2>登録が完了しました。</h2>

					<p>'.$_POST["userName"].'様、ご登録ありがとうございます。</p>
					<p>'.$_POST["userMailAddress"].'宛に、登録確認メールを送信いたしました。</p>
					<p>万一、登録されたメールアドレスに確認メールが届かない場合は、管理者：石﨑健太(<a href = "mailto:mail@red2019april.com">mail@red2019april.com</a>)までお問い合わせください。</p>

					<div>
						<form action = "home.php" method = "post">
							<input type = "hidden" name = "userId" value = "'.htmlspecialchars ( $_POST['newUserId'] ).'">
							<input type = "hidden" name = "password" value = "'.htmlspecialchars ( $_POST['newPassword'] ).'">
							<input type = "submit" value = "ホーム画面">
						</form>
					</div>
				';

			}

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