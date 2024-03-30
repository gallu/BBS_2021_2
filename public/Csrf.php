<?php  // Csrf.php
/**
// 設定する時
$token = Csrf::set();

// チェックする時
$r = Csrf::check();
if (false === $r) {
    だめぽ
}
 */
class Csrf {
    //
    const SESSION_KEY = 'csrf_token';
    
    //
    public static function set() {
        // tokenを作る
        $token = bin2hex( random_bytes(24) );

        // CSRFトークンは5個まで(で後で追加するので、ここでは4個以下に)
        while (5 <= count($_SESSION[self::SESSION_KEY] ?? [])) {
            array_shift($_SESSION[self::SESSION_KEY]);
        }

        // tokenをセッションに格納する
        $_SESSION[self::SESSION_KEY][$token] = time();

        // tokenをreturnする
        return $token;
    }

    //
    public static function check() {
        // POSTから入ってきたkeyの把握
        $csrf_token = strval($_POST['csrf_token'] ?? '');
        // そもそもtokenがなければNG
        if ('' === $csrf_token) {
            return false;
        }

        // keyの存在チェック + 寿命を把握
        $ttl = $_SESSION[self::SESSION_KEY][$csrf_token] ?? 0;
        // 先にトークンは削除(使い捨てなので)
        unset($_SESSION[self::SESSION_KEY][$csrf_token]);

        // 寿命チェック(60分以内)
        if (time() >=  $ttl + (60 * 60)) {
            return false;
        }

        // 「存在する」「寿命もOK」なら、CSRFチェックOK
        return true;
    }
}


