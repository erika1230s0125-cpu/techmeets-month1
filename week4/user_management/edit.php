<?php
require_once 'db.php';

// id は正の整数だけを受け付ける（不正な値なら一覧に戻す）
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    header('Location: index.php');
    exit;
}

$error = '';

$conn = getDBConnection();

// SQLインジェクション対策：プリペアドステートメントで編集対象のユーザーを取得
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();

// ユーザーが存在しない場合は一覧に戻す
if (!$user) {
    $conn->close();
    header('Location: index.php');
    exit;
}

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
        // SQLインジェクション対策：プリペアドステートメントで更新
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, age = ? WHERE id = ?");
        // "ssii" = string, string, integer, integer
        $stmt->bind_param("ssii", $username, $email, $age, $id);

        try {
            $stmt->execute();
            $stmt->close();
            $conn->close();
            header('Location: index.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            // DBの詳細なエラー内容は画面に出さず、ログにだけ残す
            error_log($e->getMessage());
            $error = '更新に失敗しました。';
            $stmt->close();
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー編集</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>ユーザー編集</h1>
    <a href="index.php">← 一覧に戻る</a>

    <?php if ($error): ?>
    <p style="color: red;"><?php echo h($error); ?></p>
    <?php endif; ?>

    <!-- value には「POSTで送られた値」か「DBから取得した現在の値」を表示する -->
    <form method="POST">
        <label>ユーザー名:
            <input type="text" name="username"
                value="<?php echo h($_POST['username'] ?? $user['username']); ?>">
        </label><br>
        <label>メール:
            <input type="email" name="email"
                value="<?php echo h($_POST['email'] ?? $user['email']); ?>">
        </label><br>
        <label>年齢:
            <input type="number" name="age"
                value="<?php echo h($_POST['age'] ?? $user['age']); ?>">
        </label><br>
        <button type="submit">更新する</button>
    </form>
</body>
</html>
