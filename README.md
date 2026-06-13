# 教材進捗管理アプリ

- ユーザー毎に教材全チャプターの進捗を管理できます。
- メモ、フラグ機能により、質問、勉強時に不明箇所が可視化できます。

---

## Features

- ユーザー登録
- チャプターステータス登録
- メモ登録

---

## Tech Stack

- PHP 8.x
- Laravel 10.x
- MySQL 8.x
- Docker / Sail
- PHPUnit

---

## Requirements

- Docker
- Docker Compose
- PHP 8.x（Docker利用時は不要）

---

## Installation

### リポジトリをクローン

```bash
git clone https://github.com/Takayama0422/task-manager-app.git
cd task-manager-app
```

### 依存関係をインストール

```bash
composer install
```

### 環境変数ファイルを作成

```bash
cp .env.example .env
```

### アプリケーションキーを生成

```bash
sail artisan key:generate
```

### コンテナを起動

```bash
./vendor/bin/sail up -d
```

### マイグレーション

```bash
./vendor/bin/sail artisan migrate
```

---

## Running

### アプリケーション起動

```bash
./vendor/bin/sail up -d
```

### アクセス

```
http://localhost
```

---

## Database

### マイグレーション

```bash
./vendor/bin/sail artisan migrate
```

### シーダー実行

```bash
./vendor/bin/sail artisan db:seed
```

### リフレッシュ

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

---

## Testing

### 全テスト実行

```bash
./vendor/bin/sail artisan test
```

### Featureテスト

```bash
./vendor/bin/sail artisan test --testsuite=Feature
```

### 特定ファイルのみ

```bash
./vendor/bin/sail artisan test tests/Feature/ExampleTest.php
```

---
