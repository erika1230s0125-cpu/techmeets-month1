<?php
require_once 'db.php';

$error = '';

// フォームが送信されたとき（POSTリクエスト）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $ageInput = trim($_POST['age'] ?? '');

    // 年齢を整数に変換する（整数でなければ false になる）
    $age = filter_var($ageInput, FILTER_VALIDATE_INT);

    if ($username === '' || $email === '' || $ageInput === '') {
        $error = 'ユーザー名・メールアドレス・年齢は必須です。';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'メールアドレスの形式が正しくありません。';
    } elseif ($age === false || $age < 0) {
        $error = '年齢は0以上の整数で入力してください。';
    } else {
        $conn = getDBConnection();

        // SQLインジェクション対策：プリペアドステートメント
        // SQL文の中に値を直接埋め込まず、? プレースホルダに後から値をバインドする
        $stmt = $conn->prepare("INSERT INTO users (username, email, age) VALUES (?, ?, ?)");
        // "ssi" = string, string, integer
        $stmt->bind_param("ssi", $username, $email, $age);

        try {
            $stmt->execute();
            $stmt->close();
            $conn->close();
            // 登録成功したら一覧ページに移動する
            header('Location: index.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            // DBの詳細なエラー内容は画面に出さず、ログにだけ残す
            error_log($e->getMessage());
            $error = '登録に失敗しました。';
            $stmt->close();
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>新規ユーザー登録</h1>
    <a href="index.php">← 一覧に戻る</a>

    <?php if ($error): ?>
    <p style="color: red;"><?php echo h($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>ユーザー名:
            <input type="text" name="username" value="<?php echo h($_POST['username'] ?? ''); ?>">
        </label><br>
        <label>メール:
            <input type="email" name="email" value="<?php echo h($_POST['email'] ?? ''); ?>">
        </label><br>
        <label>年齢:
            <input type="number" name="age" value="<?php echo h($_POST['age'] ?? ''); ?>">
        </label><br>
        <button type="submit">登録する</button>
    </form>
</body>
</html>
