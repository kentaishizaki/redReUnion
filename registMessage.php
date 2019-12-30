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

		<title>ひとこと伝言投稿 | REDチーム連携システム「ReUnion」</title>

	</head>

	<body>


        <?php
        
		try {

			// データベース(MySQL)へ接続
			$db = new PDO ( "mysql:dbname=table_name;host=db_host;","user","password");
			// 日時の取得
			$insert_date = date('Y-m-d H:i:s');
			// SQL文のプリコンパイル
			$data = $db -> prepare ( "insert into message ( insertDate, userId, message ) values ( ?, ?, ? ); ");
			// プレースホルダに値を代入
			$data -> bindValue ( 1, $insert_date );
			$data -> bindValue ( 2, htmlspecialchars ( $_POST['userId'] ) );
			$data -> bindValue ( 3, htmlspecialchars ( $_POST['message'] ) );
			// Postメソッドで受け取ったデータをデータベースへ追加
			$data -> execute ();

			// 更新情報をログファイルに書き込む
			$log = file_get_contents ( 'log/updates.log' );
			$log .= ",0";
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