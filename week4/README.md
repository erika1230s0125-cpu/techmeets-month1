# 📝 Week4 データベース基礎（MySQL）

PHP と MySQL を使って、データの登録・一覧・編集・削除（CRUD）ができる Web アプリを2つ作成した。

- **基本課題**：ユーザー管理システム（`user_management/`）
- **実践課題（選択肢A）**：記事投稿システム（`post_management/`）

---

## ✨ 基本課題：ユーザー管理システム

| 操作 | ファイル | 内容 |
|---|---|---|
| 一覧（Read） | `index.php` | 全ユーザーを登録日の新しい順に表形式で表示 |
| 登録（Create） | `create.php` | ユーザー名・メール・年齢を入力して登録。未入力や形式エラーはメッセージを表示 |
| 編集（Update） | `edit.php` | 現在の値をフォームに表示し、変更内容を保存 |
| 削除（Delete） | `delete.php` | 確認ダイアログで「OK」を押したときだけ削除 |

---

## 📰 実践課題：記事投稿システム

| 操作 | ファイル | 内容 |
|---|---|---|
| 一覧（Read） | `index.php` | 全記事を作成日の新しい順に表示。本文は先頭だけを表示し、タイトルから詳細ページへ移動できる |
| 詳細（Read） | `show.php` | 1件の記事を表示。本文の改行もそのまま表示する |
| 投稿（Create） | `create.php` | タイトル・本文・作成者を入力して投稿。未入力や文字数オーバーはメッセージを表示 |
| 編集（Update） | `edit.php` | 現在の内容をフォームに表示し、保存後はその記事の詳細ページに戻る |
| 削除（Delete） | `delete.php` | 記事のタイトルを表示した確認ダイアログで「OK」を押したときだけ削除 |

### 工夫したこと

- **本文の改行を表示する**：`nl2br(h(...))` の順で、エスケープしてから改行を `<br>` に変換した
- **一覧の本文を短くする**：`mb_strimwidth()` で先頭だけを表示し、全文は詳細ページで読めるようにした
- **日本語の文字数を正しく数える**：`mb_strlen()` でタイトル（200文字まで）と作成者（50文字まで）の長さをチェックした
- **存在しない記事を開いたとき**：一覧ページに戻すようにした

---

## 🔒 セキュリティ対策（2つのアプリ共通）

- **SQLインジェクション対策**：すべての SQL 文でプリペアドステートメント（`prepare()` / `bind_param()`）を使用
- **XSS対策**：画面に出力する値はすべて `h()` 関数（`htmlspecialchars()` に `ENT_QUOTES` と UTF-8 を指定）を通している
- **入力チェック**：`filter_var()` で id・年齢・メールアドレスの形式を、`mb_strlen()` で記事のタイトル・作成者の文字数を確認
- **削除は POST のみ受け付ける**：URL を開いただけで削除されないようにした
- **エラー内容を画面に出さない**：DB のエラーはログに記録し、画面には簡単なメッセージだけを表示

---

## 📁 ファイル構成

```
week4/
├── docker-compose.yml      # MySQL と phpMyAdmin の起動設定
├── db_test.php             # DB 接続確認用
├── user_management/
│   ├── db.php              # DB 接続処理と h() 関数
│   ├── index.php           # ユーザー一覧
│   ├── create.php          # ユーザー登録
│   ├── edit.php            # ユーザー編集
│   ├── delete.php          # ユーザー削除
│   └── style.css           # スタイル
├── post_management/
│   ├── db.php              # DB 接続処理と h() 関数
│   ├── index.php           # 記事一覧
│   ├── show.php            # 記事詳細
│   ├── create.php          # 記事投稿
│   ├── edit.php            # 記事編集
│   ├── delete.php          # 記事削除
│   └── style.css           # スタイル
└── README.md
```

---

## 🚀 起動方法

1. Docker Desktop を起動し、「Engine running」になるまで待つ
2. `week4` フォルダでコンテナを起動する
   ```bash
   docker-compose up -d
   ```
3. http://localhost:8080（phpMyAdmin、`root` / `root`）で `myapp_db` に `users` テーブルを作成する（初回のみ）
   ```sql
   CREATE TABLE users (
       id INT PRIMARY KEY AUTO_INCREMENT,
       username VARCHAR(50) NOT NULL UNIQUE,
       email VARCHAR(100) NOT NULL UNIQUE,
       age INT,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
   );
   ```
   記事投稿システムを使う場合は、`posts` テーブルも作成する（初回のみ）
   ```sql
   CREATE TABLE posts (
       id INT PRIMARY KEY AUTO_INCREMENT,
       title VARCHAR(200) NOT NULL,
       content TEXT NOT NULL,
       author VARCHAR(50) NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
   );
   ```
4. VS Code で `user_management/index.php`（記事投稿システムは `post_management/index.php`）を右クリック →「PHP Server: Serve Project」
5. 作業が終わったらコンテナを停止する
   ```bash
   docker-compose down
   ```

---

## 📚 今週学んだこと

1. **データベースの基本**
   - データを永続的に保存するためにデータベースを使う。テーブル・カラム・レコード・主キーの考え方を学んだ。
2. **SQL の基本文法と CRUD**
   - `SELECT` / `INSERT` / `UPDATE` / `DELETE` で、データの取得・追加・更新・削除ができる。
3. **SQLインジェクション対策**
   - ユーザーの入力を SQL に直接埋め込まず、プリペアドステートメントを使う必要がある。

---

## 😣 難しかった点

1. Docker Desktopがなかなか起動せず、時間がかかってしまった。AIに相談しPCのアップデートを行い起動することに成功した。
2. 基本課題の進め方について、何から進めていけばいいか分からず詰まってしまった。進め方についてClaudeを活用しながら、課題の条件に合うようにコードを修正していくことができた。

---

## 🚀 次に活かしたいこと

- [ ] ユーザー登録から編集・削除の仕組みについて、実際にSQL文を送り結果を確かめながらどのようにデータが変化していくか知ることができた。コードを作成した際も実際にブラウザで動きを確認・検証しながら進めていくことを心掛けていきたい。 
