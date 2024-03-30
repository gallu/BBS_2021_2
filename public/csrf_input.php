<?php   // csrf_input.php
ob_start();
session_start();

// CSRF用のトークンの作成と設定
$csrf_token = bin2hex( random_bytes(24) );
// CSRFトークンは5個まで(で後で追加するので、ここでは4個以下に)
while (5 <= count($_SESSION['csrf_token'] ?? [])) {
    array_shift($_SESSION['csrf_token']);
}

// セッションに格納
$_SESSION['csrf_token'][$csrf_token] = time();
//var_dump($_SESSION);

//
ob_end_flush();
?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>CSRFテスト</title>
</head>
<body>
いらっしゃいませ。<br>
<form name="attackform" method="post" action="https://dev2.m-fr.net/furu/php_2/BBS/csrf_ok.php">
題名：<input type="text" name="title"><br>
本文：<input type="text" name="article"><br>
  <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
<br>
<input type="submit" value="送信">
</form>
</body>
</html>
