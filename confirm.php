<?php session_start(); ?>
<?php
//mail送信関数読み込み
require('mail.php');
//戻るボタン処理
if(isset($_POST['back'])){
    $_SESSION['back'] = true;
    header('Location: index.php');
}
//送信ボタン処理
if(isset($_POST['submit'])){
    //POST掃除
    refreshPost();
    //メール送信
    sendMail();
    //セッション破壊
    sessionDestroy();
    header('Location: thanks.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta charset="utf-8">
        <title></title>
        <meta name="keywords" content="">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <link rel="stylesheet" href="css/style.css">

        <!--[if lt IE 9]>
<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
    </head>
    <body id="confirm">
        <div class="container">

            <main>
                <h1>Form Sample</h1>
                <div class="form_wrap">
                    <section>
                        <form action="confirm.php" method="post" novalidate accept-charset="utf-8">

                            <p class="mt10"><span class="bold">氏名　　　　　　　　　：</span><span><?php echo h($_SESSION['name']); ?></span></p>
                            <p class="mt10"><span class="bold">氏名（ふりがな）　　　：</span><span><?php echo h($_SESSION['kana']); ?></span></p>
                            <p class="mt10"><span class="bold">メールアドレス　　　　：</span><span><?php echo h($_SESSION['email']); ?></span></p>
                            <p class="mt10"><span class="bold">アンケート　　　　　　：</span><span><?php echo h($_SESSION['question1']); ?></span></p>

                            <p class="mt10"><span class="bold">ご意見・ご質問</span></p>
                            <p class="mt10"><?php echo nl2br(h($_SESSION['question2'])); ?></p>


                            <div class="btn_area">
                                <?php echo confirmOutput($_SESSION);?>

                                <input type="hidden" name="confirm" value="confirm_submit">
                                <input name="submit" value="送信" class="btn_submit" type="submit"><br>
                                <input name="back" class="btn_back" type="submit" value="戻って編集">
                            </div>

                        </form>

                </div>
            </main>

        </div><!-- container-->

    </body>
</html>
<?php
//確認画面の入力内容出力用関数
function confirmOutput($arr){
    $html = '';
    foreach($arr as $key => $val) {
        $out = '';
        if(is_array($val)){
            foreach($val as $out){
                $html .= '<input type="hidden" name="'.$key.'[]" value="'.str_replace(array("<br />","<br>"),"",$out).'" />';
            }
        }else{
            $out = $val;
            $html .= '<input type="hidden" name="'.$key.'" value="'.str_replace(array("<br />","<br>"),"",$out).'" />';
        }
    }
    return $html;
}
//セッション破壊
function sessionDestroy(){
    // セッション変数を全て解除する
    $_SESSION = array();
    // セッションを切断するにはセッションクッキーも削除する。
    // Note: セッション情報だけでなくセッションを破壊する。
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
                  $params["path"], $params["domain"],
                  $params["secure"], $params["httponly"]
                 );
    }
    // 最終的に、セッションを破壊する
    session_destroy();
}
?>
