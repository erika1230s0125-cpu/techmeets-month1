<?php
require_once 'db.php';

// 削除は POST のときだけ受け付ける
// （GET で削除できると、URLを開いただけで確認ダイアログなしに削除されてしまうため）
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// id は正の整数だけを受け付ける（不正な値なら一覧に戻す）
$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();

// SQLインジェクション対策：プリペアドステートメントで削除
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

// 削除後は一覧ページに移動
header('Location: index.php');
exit;
