$( function () {

	// メニュー「ひとこと伝言板」クリックで動作
	$('#menuMessage').on ( 'click', function () {

		// ひとこと伝言板を表示
		$('#message').show ();

		// メンバー近況報告を非表示
		$('#recentUpdate').hide ();

		// 問い合わせ画面を非表示
		$('#inquiry').hide ();

	} );

	// メニュー「メンバー近況報告」クリックで動作
	$('#menuRecentUpdate').on ( 'click', function () {

		// ひとこと伝言板を非表示
		$('#message').hide ();

		// メンバー近況報告を表示
		$('#recentUpdate').show ();

		// 問い合わせ画面を非表示
		$('#inquiry').hide ();

	} );

	// メニュー「管理者(石﨑健太)へ連絡」クリックで動作
	$('#menuInquiry').on ( 'click', function () {

		// ひとこと伝言板を非表示
		$('#message').hide ();

		// メンバー近況報告を非表示
		$('#recentUpdate').hide ();

		// 問い合わせ画面を表示
		$('#inquiry').show ();

	} );

} );
