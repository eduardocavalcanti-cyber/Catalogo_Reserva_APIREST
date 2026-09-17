# Guia rápido

1. Abra esta pasta no VS Code.
2. Crie `.env` copiando `.env.example`.
3. Execute `database/banco.sql` no MySQL.
4. No terminal da pasta, rode `composer install`.
5. Rode `php -S localhost:8000 -t public`.
6. No Insomnia, faça GET `http://localhost:8000/api/itens`.
7. Para POST, use `Content-Type: application/json`.
