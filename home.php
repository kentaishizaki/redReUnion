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

		<script type = "text/javascript" src = "//typesquare.com/3/tsst/script/ja/typesquare.js?5d0c5d788f244c49a2e341b4ac1e02ec" charset = "utf-8"></script>

		<script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
		<script type = "text/javascript" src = "js/menu.js"></script>

		<title>会員ホーム | REDチーム連携システム「ReUnion」</title>

	</head>

	<body>

		<div id = "main">

			<?php

				try {
					// データベース(MySQL)へ接続
					$db = new PDO ( "mysql:dbname=table_name;host=db_host;","user","password");
					// SQL文のプリコンパイル（SQLインジェクション防止）
					$data = $db -> prepare ( "select userId, password from userInfo where userId = ?" );   // ログイン認証
					// プレースホルダに値を代入（ヴァリデーションの実施）
					$data -> bindValue ( 1, htmlspecialchars ( $_POST['userId'] ) );
					// データベース上のデータを取得
					$data -> execute ();
					// 取得したデータを変数に保管（ユーザIDが存在しない場合はnull）
					$userInfo = $data -> fetch ();

					// データベース接続の切断
					$db = null;

					// ログイン認証
					if ( password_verify ( htmlspecialchars ( $_POST['password'] ), $userInfo['password'] ) ) {	// パスワードが正しい場合

						// 認証情報をセッション変数へ保管（当セッションにおいて、再度home.jspにアクセスした際に認証作業を行わないため）
						$_SESSION['userId'] = htmlspecialchars ( $_POST['userId'] );
						$_SESSION['password'] = htmlspecialchars ( $_POST['password'] );

						if ( $_SESSION['userId'] == "Sanbancho1-1" ) {

							echo '

								<h1>RED2019ReUnion&ensp;新規入会</h1>

								<form class = "background" action = "join.php" method = "post">
									<table class = "input">
										<tr><td class = "left">お名前<span>漢字・ひらがな・カタカナ1文字以上30文字以内</span></td><td class = "right"><input type = "text" name = "userName" value = "'.$_POST['userName'].'" pattern = "^([ぁ-んァ-ンー\u4E00-\u9FFF]{1,30})$" required></td>
										<tr><td class = "left">メールアドレス</td><td class = "right"><input type = "text" name = "userMailAddress" value = "'.$_POST['userMailAddress'].'" pattern = "^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$" required></td>
										<tr><td class = "left">ユーザID<span>半角英数字3文字以上</span></td><td class = "right"><input type = "text" name = "newUserId" value = "'.$_POST['newUserId'].'" pattern = "^([a-zA-Z0-9]{3,30})$" required></td>
										<tr><td class = "left">パスワード<span>半角英数字8文字以上</span></td><td class = "right"><input type = "password" name = "newPassword" value = "'.$_POST['newPassword'].'" pattern = "^([a-zA-Z0-9]{8,})$" required></td>
										<tr><td class = "submit" colspan = "2"><input type = "submit" value = "登録"></td></tr>
									</table>
								</form>

								<h2>登録がうまくできないときは</h2>
								<p>管理者：石崎健太(<a href = "mailto:mail@red2019april.com">mail@red2019april.com</a>)までご連絡ください。</p>
                                                                          
							';

						}
						else {

							echo '

								<div id = "menu">

									<h1>RED2019ReUnion&ensp;会員ホーム</h1>

									<input id = "menuMessage" type = "button" value = "ひとこと伝言板">
									<input id = "menuRecentUpdate" type = "button" value = "メンバーの近況">
									<input id = "menuInquiry" type = "button" value = "リーダーへ連絡">

								</div>

								<div id = "message">

									<h2>ひとこと伝言板</h2>

									<p>「飲み会のかけ声」「最近勉強していること」なんでもOK!</p>

									<form class = "background" action = "registMessage.php" method = "post">
										<input type = "hidden" name = "userId" value = "'.$_SESSION['userId'].'">
										<table class = "input">
											<tr><td class = "messageBody" colspan = "2"><textarea type = "text" name = "message" placeholder = "メッセージを入力"></textarea></td>
											<tr><td class = "submit" colspan = "2"><input type = "submit" value = "投稿"></td></tr>
										</table>
									</form>

							';

								// データベース(MySQL)へ接続
								$db = new PDO ( "mysql:dbname=table_name;host=db_host;", "user", "password" );
								// データベース上のデータを取得
								$data = $db -> prepare ( "select message.message, userInfo.name, message.insertDate from message left join userInfo on message.userId = userInfo.userId where message.delFlg = 0 order by insertDate desc" );
								// データベース上のデータを取得
								$data -> execute ();
								// データベース接続の切断
								$db = null;

								echo '
									<h3>ひとこと伝言一覧</h3>
								';

								// データを配列に代入
								while ( $row = $data -> fetch() ) {
									// データを表示
									echo '
										<div class = "contents">
											<table class = "contents">
												<tr><td class = "left author"><span class = "author">'.$row["name"].'さん</span></td><td class = "right author">'.$row["insertDate"].'</td></tr>
												<tr><td class = "messageBody" colspan = "2">'.nl2br ( $row["message"], false ).'</td></tr>
											</table>
										</div>
									';
								}

							echo '

								</div>

							';

							echo '

								<div id = "recentUpdate">

									<h2>メンバーの近況</h2>

									<p>入力は、公開したい内容だけでOK!</p>

									<form class = "background" action = "registRecentUpdate.php" method = "post">
										<input type = "hidden" name = "userId" value = "'.$_SESSION['userId'].'">
										<table class = "input">
											<tr><td class = "left">仕事（職場・業務など）</td><td class = "right"><input type = "text" name = "job"></td>
											<tr><td class = "left">メールアドレス</td><td class = "right"><input type = "text" name = "mailAddress"></td>
											<tr><td class = "left">LINE&ensp;ID</td><td class = "right"><input type = "text" name = "lineId"></td>
											<tr><td class = "left">Facebook&ensp;ID</td><td class = "right"><input type = "text" name = "facebookId" placeholder = "facebook.com/に続けて入力"></td>
											<tr><td class = "left">Twitter&ensp;ID</td><td class = "right"><input type = "text" name = "twitterId" placeholder = "@に続けて入力"></td>
											<tr><td class = "left">住所</td><td class = "right"><input type = "text" name = "address" placeholder = "都道府県のみでもOK!"></td>
											<tr><td class = "left">電話番号</td><td class = "right"><input type = "text" name = "telNumber"></td>
											<tr><td class = "submit" colspan = "2"><input type = "submit" value = "登録"></td></tr>
										</table>
									</form>

							';

								// データベース(MySQL)へ接続
								$db = new PDO ( "mysql:dbname=table_name;host=db_host;", "user", "password" );
								// データベース上のデータを取得
								$data = $db -> prepare ( "select recentUpdate.job, recentUpdate.mailAddress, recentUpdate.lineId, recentUpdate.facebookId, recentUpdate.twitterId, recentUpdate.address, recentUpdate.telNumber, recentUpdate.insertDate, recentUpdate.delFlg, userInfo.name from recentUpdate left join userInfo on recentUpdate.userId = userInfo.userId where recentUpdate.delFlg = 0 order by insertDate desc" );
								// データベース上のデータを取得
								$data -> execute ();
								// データベース接続の切断
								$db = null;

								echo '
									<h3>みんなの近況</h3>
								';

								// データを配列に代入
								while ( $row = $data -> fetch() ) {
									// データを表示
									echo '
										<div class = "contents">
											<table class = "contents">
												<tr><td class = "left author"><span class = "author">'.$row["name"].'さん</span></td><td class = "right author">'.$row["insertDate"].'</td></tr>
												<tr><td class = "left">仕事（職場・業務など）</td><td class = "right">'.nl2br ( $row["job"], false ).'</td></tr>
												<tr><td class = "left">メールアドレス</td><td class = "right"><a href = "mailto:'.$row["mailAddress"].'">'.nl2br ( $row["mailAddress"], false ).'</a></td></tr>
												<tr><td class = "left">LINE&ensp;ID</td><td class = "right">'.nl2br ( $row["lineId"], false ).'</td></tr>
												<tr><td class = "left">Facebook&ensp;ID</td><td class = "right"><a href = "https://www.facebook.com/'.$row["facebookId"].'" target = "_blank">'.nl2br ( $row["facebookId"], false ).'</a></td></tr>
												<tr><td class = "left">Twitter&ensp;ID</td><td class = "right"><a href = "https://twitter.com/'.$row["twitterId"].'" target = "_blank">'.nl2br ( $row["twitterId"], false ).'</a></td></tr>
												<tr><td class = "left">住所</td><td class = "right">'.nl2br ( $row["address"], false ).'</td></tr>
												<tr><td class = "left">電話番号</td><td class = "right">'.nl2br ( $row["telNumber"], false ).'</td></tr>
											</table>
										</div>
									';
								}

							echo '

								</div>

							';

							echo '

								<div id = "inquiry">

									<h2>リーダーへ連絡</h2>

									<p>石﨑健太へ御用の際はこちらから</p>

									<form class = "background" action = "sendToAdmin.php" method = "post">
										<input type = "hidden" name = "userId" value = "'.$_SESSION['userId'].'">
										<table class = "input">
											<tr><td class = "messageBody" colspan = "2"><textarea type = "text" name = "message" placeholder = "メッセージを入力"></textarea></td>
											<tr><td class = "submit" colspan = "2"><input type = "submit" value = "送信"></td></tr>
										</table>
									</form>

								</div>

							';

						}

					}
					else {	// パスワードが一致しない（データがnullである）場合

						echo '<p>認証に失敗しました。</p>';

					}

				}
				catch ( PDOException $e ) {

					// エラー内容の表示
					echo $e -> getMessage ();

					// 処理の終了
					die ();

				}
			?>
		
		</div>
		
	</body>

</html>
