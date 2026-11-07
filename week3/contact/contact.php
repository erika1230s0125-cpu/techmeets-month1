<?php
// XSS対策：画面に出力する値は必ずこの関数を通してエスケープする
// < > " ' & を HTML エンティティに変換し、タグやスクリプトとして解釈されないようにする
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

// 日本語も1文字として数える文字数カウント（mbstring 拡張がない環境でも動くよう正規表現で数える）
function str_length($str) {
    return preg_match_all("/./us", $str);
}

// 入力値とエラーメッセージの初期値
$name    = "";
$email   = "";
$subject = "";
$message = "";
$errors  = [];
$sent    = false;

// フォームが送信されたときだけ処理する
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 未送信の項目があってもエラーにならないよう ?? で空文字を入れる
    // 前後の空白は trim で取り除く
    $name    = trim($_POST["name"] ?? "");
    $email   = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    // 空入力ガード：trim は全角スペースを取り除かないため、空白だけの入力は空文字として扱う
    if (preg_match("/\A\s*\z/u", $name))    { $name = ""; }
    if (preg_match("/\A\s*\z/u", $email))   { $email = ""; }
    if (preg_match("/\A\s*\z/u", $subject)) { $subject = ""; }
    if (preg_match("/\A\s*\z/u", $message)) { $message = ""; }

    // 入力チェック
    if ($name === "") {
        $errors["name"] = "名前を入力してください。";
    } elseif (str_length($name) > 50) {
        $errors["name"] = "名前は50文字以内で入力してください。";
    }

    if ($email === "") {
        $errors["email"] = "メールアドレスを入力してください。";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "メールアドレスの形式が正しくありません。";
    }

    if ($subject === "") {
        $errors["subject"] = "件名を入力してください。";
    } elseif (str_length($subject) > 100) {
        $errors["subject"] = "件名は100文字以内で入力してください。";
    }

    if ($message === "") {
        $errors["message"] = "メッセージを入力してください。";
    } elseif (str_length($message) > 1000) {
        $errors["message"] = "メッセージは1000文字以内で入力してください。";
    }

    // エラーがなければ送信完了とする
    if (empty($errors)) {
        $sent = true;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>お問い合わせ</title>
<style>
  body { font-family: sans-serif; padding: 24px; max-width: 480px; margin: 0 auto; }
  label { display: block; margin-bottom: 4px; font-weight: bold; }
  input, textarea { width: 100%; box-sizing: border-box; padding: 8px; margin-bottom: 4px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
  .field { margin-bottom: 16px; }
  .error { color: #d33; font-size: 13px; margin: 0; }
  .required { color: #d33; font-size: 12px; font-weight: normal; margin-left: 6px; }
  button { padding: 8px 20px; font-size: 14px; cursor: pointer; }
  .result { background: #f0f4ff; padding: 16px; border-radius: 6px; margin-top: 24px; }
  .result p { white-space: pre-wrap; word-break: break-all; }
</style>
</head>
<body>

<h1>お問い合わせ</h1>

<?php if ($sent): ?>
  <!-- 送信完了画面：入力内容はすべて h() でエスケープして表示する -->
  <div class="result">
    <p>以下の内容でお問い合わせを受け付けました。</p>
    <p><strong>名前：</strong><?= h($name) ?></p>
    <p><strong>メールアドレス：</strong><?= h($email) ?></p>
    <p><strong>件名：</strong><?= h($subject) ?></p>
    <p><strong>メッセージ：</strong><br><?= h($message) ?></p>
  </div>
  <p><a href="contact.php">フォームに戻る</a></p>
<?php else: ?>
  <!-- 入力画面：エラー時に入力値を value に戻すときもエスケープする -->
  <form method="POST" novalidate>
    <div class="field">
      <label for="name">名前<span class="required">必須</span></label>
      <input type="text" id="name" name="name" placeholder="山田太郎" value="<?= h($name) ?>">
      <?php if (isset($errors["name"])): ?><p class="error"><?= h($errors["name"]) ?></p><?php endif; ?>
    </div>

    <div class="field">
      <label for="email">メールアドレス<span class="required">必須</span></label>
      <input type="email" id="email" name="email" placeholder="taro@example.com" value="<?= h($email) ?>">
      <?php if (isset($errors["email"])): ?><p class="error"><?= h($errors["email"]) ?></p><?php endif; ?>
    </div>

    <div class="field">
      <label for="subject">件名<span class="required">必須</span></label>
      <input type="text" id="subject" name="subject" placeholder="お問い合わせの件名" value="<?= h($subject) ?>">
      <?php if (isset($errors["subject"])): ?><p class="error"><?= h($errors["subject"]) ?></p><?php endif; ?>
    </div>

    <div class="field">
      <label for="message">メッセージ<span class="required">必須</span></label>
      <!-- textarea は value 属性ではなくタグの間に値を入れる -->
      <textarea id="message" name="message" rows="6" placeholder="お問い合わせ内容をご記入ください"><?= h($message) ?></textarea>
      <?php if (isset($errors["message"])): ?><p class="error"><?= h($errors["message"]) ?></p><?php endif; ?>
    </div>

    <button type="submit">送信</button>
  </form>
<?php endif; ?>

</body>
</html>
