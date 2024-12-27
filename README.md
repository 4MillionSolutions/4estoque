Clone o projeto

git clone https://github.com/douglasdacosta/white_label.git

$cd white_label

Adicione o Laradock
git clone https://github.com/laradock/laradock.git
$cd laradock
$ cp .env.example .env
Na .env altere a linha (Evita conflito com o do S.O.)
WORKSPACE_SSH_PORT=9999
NGINX_HOST_HTTP_PORT=8002
NGINX_HOST_HTTPS_PORT=4433
MYSQL_DATABASE=pedidos

$sudo docker-compose up -d nginx mysql phpmyadmin

$sudo docker-compose ps

Acesso ao Banco de dados - phpmyadmin
http://localhost:1010/
Servidor: mysql
User: root
Senha: root

Crie o banco de dados pedidos

Adicionar no .ENV
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=pedidos
DB_USERNAME=root
DB_PASSWORD=root

$sudo docker-compose exec --user=laradock workspace bash
$composer install
$php artisan migrate

Para logar no projeto utilize
http://localhost:8002
Login: admin@admin.com
senha: admin 
# 4estoque
