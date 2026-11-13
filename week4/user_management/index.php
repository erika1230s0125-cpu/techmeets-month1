<?php
require_once 'db.php';
$conn = getDBConnection();

// 全ユーザーを取得
$stmt = $conn->prepare("SELECT id, username, email, age, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>ユーザー管理システム</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>ユーザー一覧</h1>
    <a href="create.php">新規ユーザー登録</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ユーザー名</th>
                <th>メール</th>
                <th>年齢</th>
                <th>登録日</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo h($row['id']); ?></td>
                <td><?php echo h($row['username']); ?></td>
                <td><?php echo h($row['email']); ?></td>
                <td><?php echo h($row['age']); ?></td>
                <td><?php echo h($row['created_at']); ?></td>
                <td>
                    <a href="edit.php?id=<?php echo h($row['id']); ?>">編集</a>
                    <!-- 削除は POST で送信し、送信前に確認ダイアログを表示する -->
                    <!-- ユーザー名は JavaScript に直接埋め込まず data-name 属性から読む（XSS対策） -->
                    <form method="POST" action="delete.php" style="display: inline;"
                          data-name="<?php echo h($row['username']); ?>"
                          onsubmit="return confirm(this.dataset.name + ' さんを本当に削除しますか？')">
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