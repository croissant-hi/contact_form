$("#main_form").validate({
    errorElement: "span",
    errorClass: "alert",
    rules: {
        name: {
            required: true
        },
        kana: {
            required: true
        },
        email: {
            required: true,
            email: true
        },
        email_check: {
            required: true,
            email: true,
            equalTo: "#email"
        }
    },
    messages: {
        name: {
            required: "氏名（漢字）をご入力ください"
        },
        kana: {
            required: "氏名（フリガナ）をご入力ください"
        },
        email: {
            required: "メールアドレスをご入力ください",
            email: "メールアドレスの形式が違います"
        },
        email_check: {
            required: "メールアドレス（確認用）をご入力ください",
            email: "メールアドレス（確認用）の形式が違います",
            equalTo: "メールアドレスが一致しません"
        }
    }
});
