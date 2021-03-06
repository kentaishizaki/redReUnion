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

		<title>近況報告登録 | REDチーム連携システム「ReUnion」</title>

	</head>

	<body>


        <?php
        
		try {

			// データベース(MySQL)へ接続
			$db = new PDO ( "mysql:dbname=table_name;host=db_host;", "user", "password" );
			// 日時の取得
			$insert_date = date('Y-m-d H:i:s');
			// SQL文のプリコンパイル
			$data = $db -> prepare ( "select * from recentUpdate where userId = ?" );
			// プレースホルダに値を代入
			$data -> bindValue ( 1, htmlspecialchars ( $_POST['userId'] ) );
			// Postメソッドで受け取ったデータをデータベースへ追加
			$data -> execute ();

			// 過去のデータを論理削除
			if ( $previous = $data -> fetch() ) {

				// SQL文のプリコンパイル
				$data = $db -> prepare ( "update recentUpdate set delFlg = 1 where userId = ?" );
				// プレースホルダに値を代入
				$data -> bindValue ( 1, htmlspecialchars ( $_POST['userId'] ) );
				// Postメソッドで受け取ったデータをデータベースへ追加
				$data -> execute ();

			}

			// 日時の取得
			$insert_date = date('Y-m-d H:i:s');
			// SQL文のプリコンパイル
			$data = $db -> prepare ( "insert into recentUpdate ( insertDate, userId, job, mailAddress, lineId, facebookId, twitterId, address, telNumber ) values ( ?, ?, ?, ?, ?, ?, ?, ?, ? ); ");
			// プレースホルダに値を代入
			$data -> bindValue ( 1, $insert_date );
			$data -> bindValue ( 2, htmlspecialchars ( $_POST['userId'] ) );
			$data -> bindValue ( 3, htmlspecialchars ( $_POST['job'] ) );
			$data -> bindValue ( 4, htmlspecialchars ( $_POST['mailAddress'] ) );
			$data -> bindValue ( 5, htmlspecialchars ( $_POST['lineId'] ) );
			$data -> bindValue ( 6, htmlspecialchars ( $_POST['facebookId'] ) );
			$data -> bindValue ( 7, htmlspecialchars ( $_POST['twitterId'] ) );
			$data -> bindValue ( 8, htmlspecialchars ( $_POST['address'] ) );
			$data -> bindValue ( 9, htmlspecialchars ( $_POST['telNumber'] ) );
			// Postメソッドで受け取ったデータをデータベースへ追加
			$data -> execute ();

			// 更新情報をログファイルに書き込む
			$log = file_get_contents ( 'log/updates.log' );
			$log .= ",1";
			file_put_contents ( 'log/updates.log', $log );

			echo '
				<h2>投稿が完了しました。</h2>

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