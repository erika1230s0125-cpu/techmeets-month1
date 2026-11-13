<?php
require_once 'db.php';

$error = '';

// フォームが送信されたとき（POSTリクエスト）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author = trim($_POST['author'] ?? '');

    if ($title === '' || $content === '' || $author === '') {
        $error = 'タイトル・内容・作成者は必須です。';
    } elseif (mb_strlen($title) > 200) {
        $error = 'タイトルは200文字以内で入力してください。';
    } elseif (mb_strlen($author) > 50) {
        $error = '作成者は50文字以内で入力してください。';
    } else {
        $conn = getDBConnection();

        // SQLインジェクション対策：プリペアドステートメント
        // SQL文の中に値を直接埋め込まず、? プレースホルダに後から値をバインドする
        $stmt = $conn->prepare("INSERT INTO posts (title, content, author) VALUES (?, ?, ?)");
        // "sss" = string, string, string
        $stmt->bind_param("sss", $title, $content, $author);

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
    <title>記事作成</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>新規記事作成</h1>
    <a href="index.php">← 一覧に戻る</a>

    <?php if ($error): ?>
    <p style="color: red;"><?php echo h($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>タイトル:
            <input type="text" name="title" value="<?php echo h($_POST['title'] ?? ''); ?>">
        </label><br>
        <label>内容:<br>
            <textarea name="content" rows="10" cols="60"><?php echo h($_POST['content'] ?? ''); ?></textarea>
        </label><br>
        <label>作成者:
            <input type="text" name="author" value="<?php echo h($_POST['author'] ?? ''); ?>">
        </label><br>
        <button type="submit">登録する</button>
    </form>
</body>
</html>
