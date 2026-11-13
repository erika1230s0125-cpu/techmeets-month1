<?php
require_once 'db.php';
$conn = getDBConnection();

// 記事一覧を取得する
$stmt = $conn->prepare("SELECT id, title, content, author, created_at FROM posts ORDER BY created_at DESC, id DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>記事管理システム</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>記事一覧</h1>
    <a href="create.php">新規記事作成</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>タイトル</th>
                <th>内容</th>
                <th>作成者</th>
                <th>作成日</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo h($row['id']); ?></td>
                <td><a href="show.php?id=<?php echo h($row['id']); ?>"><?php echo h($row['title']); ?></a></td>
                <td><?php echo h(mb_strimwidth($row['content'], 0, 50, '…')); ?></td>
                <td><?php echo h($row['author']); ?></td>
                <td><?php echo h($row['created_at']); ?></td>
                <td>
                    <a href="edit.php?id=<?php echo h($row['id']); ?>">編集</a>
                    <!-- 削除は POST で送信し、送信前に確認ダイアログを表示する -->
                    <!-- タイトル名は JavaScript に直接埋め込まず data-name 属性から読む（XSS対策） -->
                    <form method="POST" action="delete.php" style="display: inline;"
                          data-name="<?php echo h($row['title']); ?>"
                          onsubmit="return confirm('「' + this.dataset.name + '」を本当に削除しますか？')">
                        <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                        <button type="submit">削除</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>