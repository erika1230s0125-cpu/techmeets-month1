<?php
require_once 'db.php';

// id は正の整数だけを受け付ける（不正な値なら一覧に戻す）
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();

// SQLインジェクション対策：プリペアドステートメントで記事を1件取得
$stmt = $conn->prepare("SELECT id, title, content, author, created_at FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post   = $result->fetch_assoc();
$stmt->close();
$conn->close();

// 記事が存在しない場合は一覧に戻す
if (!$post) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo h($post['title']); ?> | 記事管理システム</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <a href="index.php">← 一覧に戻る</a>

    <h1><?php echo h($post['title']); ?></h1>
    <p>
        作成者：<?php echo h($post['author']); ?><br>
        作成日：<?php echo h($post['created_at']); ?>
    </p>

    <!-- 本文の改行をそのまま表示するため、エスケープした後に nl2br で <br> に変換する -->
    <div><?php echo nl2br(h($post['content'])); ?></div>

    <div>
        <a href="edit.php?id=<?php echo (int)$post['id']; ?>">編集</a>
        <!-- 削除は POST で送信し、送信前に確認ダイアログを表示する -->
        <!-- タイトル名は JavaScript に直接埋め込まず data-name 属性から読む（XSS対策） -->
        <form method="POST" action="delete.php" style="display: inline;"
              data-name="<?php echo h($post['title']); ?>"
              onsubmit="return confirm('「' + this.dataset.name + '」を本当に削除しますか？')">
            <input type="hidden" name="id" value="<?php echo (int)$post['id']; ?>">
            <button type="submit">削除</button>
        </form>
    </div>
</body>
</html>
