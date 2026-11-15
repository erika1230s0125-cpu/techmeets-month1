# 📚 techmeets Month 1

techmeets の Month 1（Week 1〜4）で作成した課題をまとめたリポジトリです。
HTML / CSS / JavaScript の基礎から始めて、PHP でのフォーム処理、MySQL を使った CRUD アプリまでを作成しました。

---

## 🛠️ 使用技術

| 分類 | 技術 |
|---|---|
| フロントエンド | HTML / CSS / JavaScript |
| バックエンド | PHP 8.x |
| データベース | MySQL 8.0（phpMyAdmin） |
| 環境 | Docker / Docker Compose |
| ツール | VS Code / Git / GitHub |

---

## ✨ 作ったもの

### Week 1：HTML / CSS / JavaScript の基礎

| 作品 | 場所 | 内容 |
|---|---|---|
| ToDoリスト | `week1/index.html` | タスクの追加/削除ができる ToDo リスト |

### Week 2：CSS レイアウト・JavaScript

| 作品 | 場所 | 内容 |
|---|---|---|
| 好きなスイーツまとめ | `week2/Challenges/` | CSS Grid / Flexbox を使ったレイアウト |
| スライドショー | `week2/experiment-1/` | 画像を切り替えられるスライドショー |
| プロフィール | `week2/experiment-2/` | 自己紹介ページ |
| ToDoリスト | `week2/experiment-3/` | 検索機能・絞り機能付きToDo リスト |

### Week 3：JavaScript の読解・PHP の基礎

| 作品 | 場所 | 内容 |
|---|---|---|
| ショッピングカート | `week3/js-practice/` | JavaScript で作ったショッピングカート |
| PHP 基礎練習 | `week3/php-practice/` | 自己紹介・コメントフォームなど。XSS などの問題を修正した `comment_fixed.php` を含む |
| お問い合わせフォーム | `week3/contact/` | 入力チェック付きのお問い合わせフォーム（1ファイルで入力・エラー・完了画面を切り替え） |

### Week 4：データベース（MySQL）

| 作品 | 場所 | 内容 |
|---|---|---|
| ユーザー管理システム | `week4/user_management/` | ユーザーの登録・一覧・編集・削除（CRUD） |
| 記事投稿システム | `week4/post_management/` | 記事の投稿・一覧・詳細・編集・削除（CRUD） |

Week 3・Week 4 の作品には、それぞれのフォルダに詳しい README があります。

---

## 🔒 意識したこと

- **XSS 対策**：画面に出力する値は `htmlspecialchars()` でエスケープする
- **SQLインジェクション対策**：SQL 文はすべてプリペアドステートメントで実行する
- **入力チェック**：必須項目・文字数・メールアドレスの形式などを確認し、エラーメッセージを表示する

---

## 📁 ディレクトリ構成

```
month1/
├── week1/          # ToDoリスト
├── week2/          # スイーツまとめ・スライドショー・プロフィール・ToDoリスト
├── week3/          # ショッピングカート・PHP基礎練習・お問い合わせフォーム
├── week4/          # ユーザー管理システム・記事投稿システム
└── README.md
```

---

## 🚀 セットアップ手順

### 1. リポジトリをクローンする

```bash
git clone https://github.com/erika1230s0125-cpu/techmeets-month1.git
cd techmeets-month1
```

### 2. 作品ごとの動かし方

| 対象 | 動かし方 |
|---|---|
| Week 1・Week 2・Week 3 の HTML | `.html` ファイルをブラウザで開く |
| Week 3 の PHP | VS Code で `.php` ファイルを右クリック →「PHP Server: Serve Project」 |
| Week 4 | 下の手順で Docker を起動してから、PHP Server で開く |

**Week 4 の起動方法**

```bash
cd week4
docker-compose up -d
```

1. http://localhost:8080（phpMyAdmin）で `users` テーブル・`posts` テーブルを作成する（SQL は `week4/README.md` を参照）
2. `user_management/index.php` または `post_management/index.php` を PHP Server で開く
3. 作業が終わったら `docker-compose down` でコンテナを停止する

---
