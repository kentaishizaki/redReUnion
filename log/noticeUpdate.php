<?php

// 更新が1件以上あった場合、処理を継続
if ( $argv[1] == "red2019" && filesize ( '/home/kenta/www/red/log/updates.log' ) ) {

	// ファイルを開く
	$file = file_get_contents ( '/home/kenta/www/red/log/updates.log' );

	// ログファイルの読み取り
	$log = explode ( ',', $file );   // （データ　0:ひとこと伝言板　1:メンバーの近況）

	// 更新件数保管のための配列の初期化
	$update = array ( 0, 0 );

	// 更新件数を記録
	foreach ( $log as $count ) {
		$update[$count]++;
	}

	// メールに記載する文章を生成
	$comment = "";   // 変数の初期化
	if ( $update[0] > 0 ) {
		$comment .= "「ひとこと伝言板」に".$update[0]."件、";
	}
	if ( $update[1] > 0 ) {
		$comment .= "「メンバーの近況」に".$update[1]."件、";
	}
	$comment .= "新しい投稿がありました。";

	// メールを生成
	$to = "mail@red2019april.com";
	$subject = "更新のおしらせ";
	$message = "<html><body><div><img src = 'https://red2019april.com/images/red.png' style = 'width: 20%;'></div><div><h2>更新のお知らせ</h2><p>".$comment."</p></div><div>ぜひご覧ください！</div><div style = 'margin-top: 10px;'>◇◆◇◆◇◆◇◆◇◆◇◆◇◆◇◆<br>RED2019&ensp;ReUnion<br><a href = 'https://red2019april.com/'>https://red2019april.com/</a><br>◇◆◇◆◇◆◇◆◇◆◇◆◇◆◇◆</div></body></html>";
	$headers = "From: mail@red2019april.com";
	$headers .= "\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8";

	// メールの送信
	mail ( $to, $subject, $message, $headers );

	// ログファイルの初期化
	$file = fopen ( '/home/kenta/www/red/log/updates.log', 'r+' );
	ftruncate ( $file, 0 );
	fclose ( $file );

}

?>
