<?php   // delete.php
//
require_once( __DIR__ . '/init.php');

//var_dump($_POST);

try {
    // 「対象投稿」のidを把握
    $bbs_id = intval($_POST['bbs_id'] ?? 0);
    if (0 >= $bbs_id) {
        throw new \Exception('');
    }
var_dump($bbs_id);

    // 削除コードを把握
    $del_code = strval($_POST['del_code'] ?? '');
    if ('' === $del_code) {
        throw new \Exception('');
    }
var_dump($del_code);

    // 「投稿」の取得
    $sql = 'SELECT * FROM bbses WHERE bbs_id=:bbs_id;';
    $pre = $dbh->prepare($sql);
    // 値のバインド
    $pre->bindValue(':bbs_id', $bbs_id, \PDO::PARAM_INT);
    // SQLの実行
    $r = $pre->execute();
    // データの取得
    $datum = $pre->fetch(PDO::FETCH_ASSOC);
    if (false === $datum) {
        throw new \Exception('');
    }
var_dump($datum);

    // del_codeのチェック
    if ($del_code !== $datum['del_code']) {
        throw new \Exception('');
    }

    // コメントを削除
    $sql = 'DELETE FROM comments WHERE bbs_id=:bbs_id;';
    $pre = $dbh->prepare($sql);
    // 値のバインド
    $pre->bindValue(':bbs_id', $bbs_id, \PDO::PARAM_INT);
    // SQLの実行
    $r = $pre->execute();
var_dump($r);

    // 対象投稿を削除
    $sql = 'DELETE FROM bbses WHERE bbs_id=:bbs_id;';
    $pre = $dbh->prepare($sql);
    // 値のバインド
    $pre->bindValue(':bbs_id', $bbs_id, \PDO::PARAM_INT);
    // SQLの実行
    $r = $pre->execute();
var_dump($r);
    // 成功メッセージの埋め込み
    $_SESSION['flash']['delete_success'] = true;
} catch(\Throwable $e) {
    // XXX 特にエラー処理なし
    echo $e->getMessage();
    // 失敗メッセージの埋め込み
    $_SESSION['flash']['delete_failure'] = true;
}

// リダイレクト
header('Location: ./index.php');
