<?php session_start(); ?>
<?php

//mail送信関数読み込み
require('mail.php');
$errm = "";
//確認ボタン処理
if(isset($_POST['confirm'])){

    if($_POST['question1'] == "その他"){
        $_POST['question1'] = $_POST['question1other'];
    }

    //内容チェック
    $errm = validateMail();

    if($errm == ""){
        foreach($_POST as $key => $val){
            $_SESSION[$key] = $val;
        }
        header('Location: confirm.php');
    }
}
//戻るボタン処理
if(isset($_SESSION['back'])){
    foreach($_SESSION as $key => $val){
        $s[$key] = $val;
    }
    unset($_SESSION['back']);
}
?>
<!DOCTYPE html>
<html lang="ja">
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

    <body id="home">
        <div class="container">

            <main>
                <h1>Form Sample</h1>
                <section class="section-box">

                    <?php if($errm)echo $errm; ?>

                    <h2>■お申し込み内容</h2>

                    <form id="main_form" action="index.php" method="post" class="size240">
                        <table class="form-table">
                            <tr>
                                <th>氏名（漢字）<span class="must">必須</span></th>
                                <td><input id="name" name="name" type="text" value="<?php if(isset($s['name'])) echo h($s['name']); ?>"></td>
                            </tr>
                            <tr>
                                <th>氏名（フリガナ）<span class="must">必須</span></th>
                                <td><input id="kana" name="kana" type="text" value="<?php if(isset($s['kana'])) echo h($s['kana']); ?>"></td>
                            </tr>
                            <tr>
                                <th>メールアドレス<span class="must">必須</span></th>
                                <td><input id="email" name="email" type="email" value="<?php if(isset($s['email'])) echo h($s['email']); ?>"></td>
                            </tr>
                            <tr>
                                <th>メールアドレス（確認用）<span class="must">必須</span></th>
                                <td><input id="email_check" name="email_check" type="email" value="<?php if(isset($s['email_check'])) echo h($s['email_check']); ?>"></td>
                            </tr>
                        </table>
                        <!-- ./form-table -->

                        <p><label>■アンケート【任意】</label></p>

                        <p>エスペランサデザインオフィスについて、皆様のご意見をおきかせください。</p>

                        <?php
                        $question1 = 1;
                        if(isset($s['question1'])){
                            if($s['question1']=="素敵"){
                                $question1 = 1;
                            }
                            if($s['question1']=="素敵じゃない"){
                                $question1 = 2;
                            }
                            if($s['question1']==$s['question1other']){
                                $question1 = 3;
                            }
                        }
                        ?>

                        <input type="radio" name="question1" value="素敵" <?php if($question1==1){echo 'checked';} ?>>素敵
                        <input type="radio" name="question1" value="素敵じゃない"<?php if($question1==2){echo 'checked';} ?>>素敵じゃない<br>
                        <input type="radio" name="question1" value="その他"<?php if($question1==3){echo 'checked';} ?>>その他&nbsp;
                        <input type="text" name="question1other" class="inputother" maxlength='100' value="<?php if(isset($s['question1other'])) echo h($s['question1other']); ?>">

                        <p class="mt50"><label>■ご意見・ご質問がありましたら下記にご記入ください（200字以内）</label></p>

                        <textarea name="question2" rows="4" maxlength="200"><?php if(isset($s['question2'])) echo h($s['question2']); ?></textarea>

                        <div class="btn_area">
                            <input type="submit" name="confirm" class="btn_confirm" value="入力内容確認"><br>
                        </div>

                    </form>

                </section>
                <!-- ./section-box -->
            </main>

        </div><!-- container-->
        <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
        <script src="js/jquery.validate.min.js"></script>
        <script src="js/validate.js"></script>
    </body>
</html>
