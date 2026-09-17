# Catálogo e Reserva de Livros/Jogos

API REST em PHP, de Eduardo Michel e Arthur Pereira.

## Tecnologias
PHP 8.3+, MySQL, Composer, Insomnia, Swagger/Swagger-PHP, Git e GitHub.

## Instalação
1. Copie `.env.example` para `.env` e configure a senha do MySQL.
2. Execute `database/banco.sql` no MySQL.
3. Execute `composer install`.
4. Execute `php -S localhost:8000 -t public`.
5. Teste no Insomnia: `GET http://localhost:8000/api/itens`.

## Endpoints
GET/POST `/api/itens`
GET/PUT/DELETE `/api/itens/{id}`
GET/POST `/api/reservas`
GET/PUT/DELETE `/api/reservas/{id}`

## Exemplo POST item
json
{"titulo":"O Pequeno Príncipe","tipo":"Livro","autor":"Antoine de Saint-Exupéry","ano":1943}


## Exemplo POST reserva
json
{"item_id":1,"nome_usuario":"Eduardo","data_reserva":"2026-09-16","status":"ativa"}


Um item reservado fica indisponível. Ao cancelar/excluir a reserva, volta a ficar disponível.
