<?php   // index.php
// https://dev2.m-fr.net/アカウント名/BBS/
// https://dev2.m-fr.net/アカウント名/BBS/index.php
require_once( __DIR__ . '/../vendor/autoload.php');
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

//
require_once( __DIR__ . '/init.php');

//
$twig = new Environment( new FilesystemLoader( __DIR__ . '/../templates') );

//
$session = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);

// 「いま」のページ数を取得(１ページスタート)
$page = intval($_GET['p'] ?? 1);
if (0 >= $page) {
    $page = 1;
}
//var_dump($page);

// 1ページあたりのitem数
$par_page = 10;

// 「前へ」のページ数
$before_page_num = $page - 1;
// 「次へ」のページ数
$next_page_num = $page + 1;

try {
    /* 投稿一覧の取得 */
    // プリペアドステートメントの作成
    $sql = 'SELECT * FROM bbses ORDER BY bbs_id DESC LIMIT :limit_num OFFSET :offset_num;';
    $pre = $dbh->prepare($sql);
    // 値のバインド
    $pre->bindValue(':limit_num', $par_page + 1, \PDO::PARAM_INT);
    $pre->bindValue(':offset_num', ($page - 1) * $par_page, \PDO::PARAM_INT);
    // SQLの実行
    $r = $pre->execute();
    // データの取得
    $list = $pre->fetchAll(PDO::FETCH_ASSOC);

    // 「次(より過去のページ)」があるかの判定
    if (count($list) === ($par_page + 1)) {
        $next_flg = true;
        array_pop($list); // 要素が１つ多いので、削る
    } else {
        $next_flg = false;
    }

    // コメントの追加
    foreach($list as $k => $v) {
        //
        $sql_comments = 'SELECT * FROM comments WHERE bbs_id = :bbs_id ORDER BY created_at DESC;';
        $pre_comments = $dbh->prepare($sql_comments);
        //
        $pre_comments->bindValue(':bbs_id', $v['bbs_id']);
        // SQLの実行
        $r = $pre_comments->execute();
        //
        $list[$k]['comments'] = $pre_comments->fetchAll(PDO::FETCH_ASSOC);
    }
    //var_dump($list);
}catch(\Throwable $e) {
    // XXX
    echo $e->getMessage();
    exit;
}

// CSRFトークンの作成
$csrf_token = Csrf::set();
//var_dump($csrf_token);

//
$context = [
    'list' => $list,
    'datum' => $session['datum'] ?? [],
    'error' => $session['error'] ?? [],
    'session' => $session,
    'page' => $page,
    'before_page_num' => $before_page_num,
    'next_page_num' => $next_page_num,
    'next_flg' => $next_flg,
    //
    'csrf_token' => $csrf_token,
];
//var_dump($context);

//
echo $twig->render('index.twig', $context);
