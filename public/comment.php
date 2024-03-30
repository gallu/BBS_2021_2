<?php   // comment.php
//
require_once( __DIR__ . '/init.php');
//var_dump($_POST); exit;

// CSRFチェック
if (false === Csrf::check()) {
    echo 'CSRF エラー！！'; // XXX 確認用
    //header('Location: ./index.php');
    exit;
}

// 入力の受け取り
$comment_body = strval($_POST['comment'] ?? '');
// validate
$error = [];
// 必須チェック
if ('' === $comment_body) {
    $error['comment_body_empty'] = true;
}
// 最大長の制限
if (300 <= mb_strlen($comment_body)) {
    $error['comment_body_max_length'] = true;
}
//
if ([] !== $error) {
    //
    $_SESSION['flash']['datum']['comment'] = $comment_body;
    $_SESSION['flash']['error'] = $error;
    //
    header('Location: ./index.php');
}
//var_dump($comment_body);

// 投稿者の情報を取得
$datum = getContributorInfo();
//var_dump($datum);

try {
    // SQL(INSERT)
    $sql = 'INSERT INTO comments(bbs_id, comment_body, user_agent, from_ip, created_at)
                VALUES(:bbs_id, :comment_body, :user_agent, :from_ip, :created_at);';
    $pre = $dbh->prepare($sql);
    //
    $pre->bindValue('bbs_id', strval($_POST['bbs_id'] ?? ''));
    $pre->bindValue('comment_body', $comment_body);
    $pre->bindValue('user_agent', $datum['user_agent']);
    $pre->bindValue('from_ip', $datum['from_ip']);
    $pre->bindValue('created_at', date('Y-m-d H:i:s'));
    // SQLを実行
    $r = $pre->execute();
    var_dump($r);
}catch(\Throwable $e) {
    // XXX
    echo $e->getMessage();
    exit;
}

// 終了処理
/*
$p = $_POST['p'] ?? '1';
$p = strval($p);
$p = rawurlencode($p);
*/
header('Location: ./index.php?p='
        . rawurlencode(strval($_POST['p'] ?? '1'))
        . '#'
        . rawurlencode(strval($_POST['bbs_id'] ?? ''))
      );









