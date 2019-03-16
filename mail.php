<?php
header("Content-Type:text/html;charset=utf-8");

$from = "";
$adminto = "";
$refrom_name = "";

//件名
$subject = "お申し込みがありました。";
$re_subject = "お申込みありがとうございます。";

// Bccで送るメールアドレス(複数指定する場合は「,」で区切ってください 例 $bccto = "aa@aa.aa,bb@bb.bb";)
$bccto = "";

$encode = "UTF-8";//このファイルの文字コード定義（変更不可）

//必須入力項目
$require_check = true;
$require = array('name','kana','email');
//メールチェック
$mail_check = true;
$mail_name = "email";
$mail_check_name = "email_check";

function sendMail(){
    global $from;
    global $adminto;
    global $refrom_name;
    global $subject;
    global $re_subject;
    global $bccto;
    global $encode;
    global $mail_name;

    //変数初期化
    $post_mail = '';
    $header ='';

    $post_mail = h($_POST[$mail_name]);

    //差出人に届くメールをセット
    $userBody = mailToUser($_POST,$encode);
    $reheader = userHeader($refrom_name,$from,$encode);
    $re_subject = "=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($re_subject,"JIS",$encode))."?=";

    //管理者宛に届くメールをセット
    $adminBody = mailToAdmin($_POST,$subject,$encode);
    $header = adminHeader($from,$bccto,$adminto);
    $subject = "=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($subject,"JIS",$encode))."?=";

    mail($adminto,$subject,$adminBody,$header);//adminあて
    mail($post_mail,$re_subject,$userBody,$reheader);//userあて


}

function validateMail(){
    global $require_check;
    global $require;
    global $mail_check;
    global $mail_name;
    global $mail_check_name;

    //変数初期化
    $empty_flag = 0;
    $errm ='';

    if($require_check){
        $res = requireCheck($require);
        $errm = $res['errm'];
        $empty_flag = $res['empty_flag'];
    }

    if($mail_check && $empty_flag != 1){
        $res = checkMail($mail_name,$mail_check_name);
        $errm = $res['errm'];
        $empty_flag = $res['empty_flag'];
    }

    return $errm;
}


//管理者宛送信メールヘッダ
function adminHeader($from,$bccto,$to){
    $header = '';
    $header="From: $from\n";
    if($bccto != '') {
        $header="Bcc: $bccto\n";
    }
    $header.="Reply-To: ".$to."\n";
    $header.="Content-Type:text/plain;charset=iso-2022-jp\nX-Mailer: PHP/".phpversion();
    return $header;
}
//管理者宛送信メールボディ
function mailToAdmin($post,$subject,$encode){
    $adminBody="「".$subject."」\n\n";
    $adminBody .="＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n\n";
    $adminBody .="【氏名　　　　　　　】{$post['name']}\n";
    $adminBody .="【氏名（フリガナ）　】{$post['kana']}\n";
    $adminBody .="【メールアドレス　　】{$post['email']}\n";
    $adminBody .="【アンケート　　　　】{$post['question1']}\n";
    $adminBody .="【ご質問・ご意見　　】\n";
    $adminBody .="{$post['question2']}\n";
    $adminBody.="\n＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n";
    $adminBody.="送信された日時：".date( "Y/m/d (D) H:i:s", time() )."\n";
    return mb_convert_encoding($adminBody,"JIS",$encode);
}
//ユーザ宛送信メールヘッダ
function userHeader($refrom_name,$from,$encode){
    $reheader = "From: ";
    if(!empty($refrom_name)){
        $default_internal_encode = mb_internal_encoding();
        if($default_internal_encode != $encode){
            mb_internal_encoding($encode);
        }
        $reheader .= mb_encode_mimeheader($refrom_name)." <".$from.">\nReply-To: ".$from;
    }else{
        $reheader .= "$from\nReply-To: ".$from;
    }
    $reheader .= "\nContent-Type: text/plain;charset=iso-2022-jp\nX-Mailer: PHP/".phpversion();
    return $reheader;
}
//ユーザ宛送信メールボディ
function mailToUser($post,$encode){
    $userBody = '';
    $userBody .= "※このメールは送信専用です。\n";
    $userBody .= "{$post['name']}様\n";
    $userBody .= "この度は、お申込み頂き、誠にありがとうございます。\n";
    $userBody .= "当日のご来場、心よりお待ち申し上げます。\n\n";
    $userBody .= "******************************************************************************\n";
    $userBody .= "≪お申込み内容≫\n";
    $userBody .= "氏名　　　{$post['name']}（{$post['kana']}）\n";
    $userBody .= "アドレス　{$post['email']}\n";
    return mb_convert_encoding($userBody,"JIS",$encode);
}

function h($string) {
    global $encode;
    return htmlspecialchars($string, ENT_QUOTES,$encode);
}
function sanitize($arr){
    if(is_array($arr)){
        return array_map('sanitize',$arr);
    }
    return str_replace("\0","",$arr);
}
//Shift-JISの場合に誤変換文字の置換関数
function sjisReplace($arr,$encode){
    foreach($arr as $key => $val){
        $key = str_replace('＼','ー',$key);
        $resArray[$key] = $val;
    }
    return $resArray;
}
function refreshPost(){
    global $encode;
    if(isset($_GET)) $_GET = sanitize($_GET);//NULLバイト除去//
    if(isset($_POST)) $_POST = sanitize($_POST);//NULLバイト除去//
    if(isset($_COOKIE)) $_COOKIE = sanitize($_COOKIE);//NULLバイト除去//
    if($encode == 'SJIS') $_POST = sjisReplace($_POST,$encode);//Shift-JISの場合に誤変換文字の置換実行

    //xxs対策
    if(isset($_POST)){
        foreach($_POST as $key => $val){
            if(is_array($val)){
                foreach($val as $key2 => $val2){
                    $val2 = sanitize($val2);
                    $val2 = h($val2);
                    $_POST[$key][$key2] = $val2;
                }
            }else{
                $val = sanitize($val);
                $val = h($val);
                $_POST[$key] = $val;
            }
        }
    }
}
//必須チェック関数
function requireCheck($require){
    $res['errm'] = '';
    $res['empty_flag'] = 0;
    foreach($require as $requireVal){
        $existsFalg = '';
        foreach($_POST as $key => $val) {
            if($key == $requireVal) {

                //連結指定の項目（配列）のための必須チェック
                if(is_array($val)){
                    $connectEmpty = 0;
                    foreach($val as $kk => $vv){
                        if(is_array($vv)){
                            foreach($vv as $kk02 => $vv02){
                                if($vv02 == ''){
                                    $connectEmpty++;
                                }
                            }
                        }

                    }
                    if($connectEmpty > 0){
                        $res['errm'] .= "<p class=\"error_messe\">【".h($key)."】は必須項目です。</p>\n";
                        $res['empty_flag'] = 1;
                    }
                }
                //デフォルト必須チェック
                elseif($val == ''){
                    $res['errm'] .= "<p class=\"error_messe\">【".h($key)."】は必須項目です。</p>\n";
                    $res['empty_flag'] = 1;
                }

                $existsFalg = 1;
                break;
            }

        }
        if($existsFalg != 1){
            $res['errm'] .= "<p class=\"error_messe\">【".$requireVal."】が未選択です。</p>\n";
            $res['empty_flag'] = 1;
        }
    }

    return $res;
}
function checkMail($email_name, $email_check_name){
    $res['errm'] = '';
    $res['empty_flag'] = 0;
    $email = $_POST[$email_name];
    $email_check = $_POST[$email_check_name];

    $mailaddress_array = explode('@',$email);
    if(!(preg_match("/^[\.!#%&\-_0-9a-zA-Z\?\/\+]+\@[!#%&\-_0-9a-z]+(\.[!#%&\-_0-9a-z]+)+$/", "$email") && count($mailaddress_array) ==2)){
        $res['errm'] .= "<p class=\"error_messe\">【".$email_name."】の形式が違います。</p>\n";
        $res['empty_flag'] = 1;
    }elseif(!($email==$email_check)){
        $res['errm'] .= "<p class=\"error_messe\">【".$email_name."】が一致しません。</p>\n";
        $res['empty_flag'] = 1;
    }
    return $res;
}

function removeCircle($str){
    $str = str_replace ( "①" , "1" , $str);
    $str = str_replace ( "②" , "2" , $str);
    $str = str_replace ( "③" , "3" , $str);
    $str = str_replace ( "④" , "4" , $str);
    $str = str_replace ( "⑤" , "5" , $str);
    $str = str_replace ( "⑥" , "6" , $str);
    $str = str_replace ( "⑦" , "7" , $str);
    $str = str_replace ( "⑧" , "8" , $str);
    $str = str_replace ( "⑨" , "9" , $str);
    return $str;
}
