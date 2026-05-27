##COACHTECHフリマ

##環境構築
bashgit clone https://github.com/hashimo537/mogi11.git
Dockerビルド docker-compose up -d --build
platform: linux/amd64追加

##Laravel環境構築
docker-compose exec php bash
composer install
cp .env.example .env 環境変数を適宜変更
php artisan key:generate
php artisan migrate
php artisan db:seed
M1/M2 Mac をお使いの場合は、docker-compose.yml の該当サービスに platform: linux/amd64 を追加してください。


##使用技術（実行環境）
PHP 8.x
Laravel 8.83.29
MySQL 8.0
nginx 1.21.1
Docker / Docker Compose


![ER図](src/er.png)

##開発環境
・項目URLアプリ（トップ画面）：　http://localhost
・ユーザー登録　：　http://localhost/register
・phpMyAdmin:　http://localhost:8080/
