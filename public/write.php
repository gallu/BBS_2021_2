<?php   // write.php
//
require_once( __DIR__ . '/init.php');

// CSRFチェック
if (false === Csrf::check()) {
    //echo 'CSRF エラー！！'; // XXX 確認用
    header('Location: ./index.php');
    exit;
}

//
$param = [
    'handle' => '',
    'title' => 'must',
    'del_code' => '',
    'body' => 'must',
];
//
$datum = [];
$error = [];
foreach($param as $k => $v) {
    // formからのデータを受け取る
    $datum[$k] = strval($_POST[$k] ?? '');

    // validate
    if ('must' === $v) {
        if ('' === $datum[$k]) {
            //$error[] = $k;
            $error[$k] = true;
        }
    }
}
//var_dump($datum, $error); exit;

// エラーなら入力ページに突き返す
if ([] !== $error) {
    //
    $_SESSION['flash']['datum'] = $datum;
    $_SESSION['flash']['error'] = $error;
    //
    header('Location: ./index.php');
}

// 投稿者の情報を取得
//$datum = $datum + getContributorInfo();
$datum += getContributorInfo();
//var_dump($datum); exit;

try {
    /* DBに「formからの内容」を書き込む */
    // プリペアドステートメントを作成
    $sql = 'INSERT INTO bbses(handle, title, del_code, body, user_agent, from_ip, created_at)
              VALUES(:handle, :title, :del_code, :body, :user_agent, :from_ip, :created_at);';
    $pre = $dbh->prepare($sql);
    var_dump($pre);
    // 値をバインド
    $datum['created_at'] = date('Y-m-d H:i:s');
    foreach($datum as $k => $v) {
        $pre->bindValue(":{$k}", $v);
    }
    // SQLを実行
    $r = $pre->execute();
    var_dump($r);
}catch(\Throwable $e) {
    // XXX
    echo $e->getMessage();
    exit;
}

// 終了処理
header('Location: ./index.php');

