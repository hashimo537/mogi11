## COACHTECHフリマ

## 環境構築
git clone git@github.com:hashimo537/mogi11.git
Dockerビルド docker-compose up -d --build

## Laravel環境構築
docker-compose exec php bash
composer install
cp .env.example .env 環境変数を適宜変更
php artisan key:generate
php artisan migrate
php artisan db:seed
M1/M2 Mac をお使いの場合は、docker-compose.yml の該当サービスに platform: linux/amd64 を追加してください。


## 使用技術（実行環境）
PHP 8.x
Laravel 8.83.29
MySQL 8.0
nginx 1.21.1
Docker / Docker Compose


![ER図](src/er.png)

## 開発環境
・項目URLアプリ（トップ画面）：　http://localhost
・ユーザー登録　：　http://localhost/register
・phpMyAdmin:　http://localhost:8080/

## 概要
COACHTECH 教材 Tutorial 10-5「テスト ハンズオン演習」で作成した成果物です。
（**ここに、何を作ったかを1〜2行で書きましょう**）

## 使用技術
- PHP 8.x
- Laravel 10.x
- PHPUnit（テスト）
- Eloquent / Factory
（**他に使ったものがあれば追記してください**）

## 学んだこと
- （**自分の言葉で2〜3項目書きましょう**）
- 
- 

## 動作確認
（**どうやって動かして確認するかを記載してください**）
