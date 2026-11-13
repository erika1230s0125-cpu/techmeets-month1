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

// SQLインジェクション対策：プリペアドステートメントで編集対象の記事を取得
$stmt = $conn->prepare("SELECT id, title, content, author FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post   = $result->fetch_assoc();
$stmt->close();

// 記事が存在しない場合は一覧に戻す
if (!$post) {
    $conn->close();
    header('Location: index.php');
    exit;
}

// フォームが送信されたとき（POSTリクエスト）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author  = trim($_POST['author'] ?? '');

    if ($title === '' || $content === '' || $author === '') {
        $error = 'タイトル・内容・作成者は必須です。';
    } elseif (mb_strlen($title) > 200) {
        $error = 'タイトルは200文字以内で入力してください。';
    } elseif (mb_strlen($author) > 50) {
        $error = '作成者は50文字以内で入力してください。';
    } else {

        // SQLインジェクション対策：プリペアドステートメントで更新
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, author = ? WHERE id = ?");
        // "sssi" = string, string, string, integer
        $stmt->bind_param("sssi", $title, $content, $author, $id);

        try {
            $stmt->execute();
            $stmt->close();
            $conn->close();
            // 更新後はその記事の詳細ページに移動
            header('Location: show.php?id=' . $id);
            exit;
        } catch (mysqli_sql_exception $e) {
            // DBの詳細なエラー内容は画面に出さず、ログにだけ残す
            error_log($e->getMessage());
            $error = '更新に失敗しました。';
            $stmt->close();
            $conn->close();
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>記事編集</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>記事編集</h1>
    <a href="show.php?id=<?php echo (int)$post['id']; ?>">← 記事に戻る</a>

    <?php if ($error): ?>
    <p style="color: red;"><?php echo h($error); ?></p>
    <?php endif; ?>

    <!-- value には「POSTで送られた値」か「DBから取得した現在の値」を表示する -->
    <form method="POST">
        <label>タイトル:
            <input type="text" name="title"
                value="<?php echo h($_POST['title'] ?? $post['title']); ?>">
        </label><br>
        <label>内容:<br>
            <textarea name="content" rows="10" cols="60"><?php echo h($_POST['content'] ?? $post['content']); ?></textarea>
        </label><br>
        <label>作成者:
            <input type="text" name="author"
                value="<?php echo h($_POST['author'] ?? $post['author']); ?>">
        </label><br>
        <button type="submit">更新する</button>
    </form>
</body>
</html>
