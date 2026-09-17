<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Item.php';
require_once __DIR__ . '/../models/Reserva.php';
require_once __DIR__ . '/../controllers/ItemController.php';
require_once __DIR__ . '/../controllers/ReservaController.php';
function responderErro(string $m, int $s): void
{
    http_response_code($s);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['erro' => $m], JSON_UNESCAPED_UNICODE);
}
function executarRotas(): void
{
    $metodo = $_SERVER['REQUEST_METHOD'];
    $caminho = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $dados = json_decode(file_get_contents('php://input'), true);
    $dados = is_array($dados) ? $dados : [];
    $rotaValida = ($caminho === '/api/itens' && in_array($metodo, ['GET', 'POST'], true)) || (preg_match('#^/api/itens/\\d+$#', $caminho) && in_array($metodo, ['GET', 'PUT', 'DELETE'], true)) || ($caminho === '/api/reservas' && in_array($metodo, ['GET', 'POST'], true)) || (preg_match('#^/api/reservas/\\d+$#', $caminho) && in_array($metodo, ['GET', 'PUT', 'DELETE'], true));
    if (!$rotaValida) {
        responderErro('Rota não encontrada', 404);
        return;
    }
    try {
        $banco = conectarBanco();
        $item = new Item($banco);
        $ic = new ItemController($item);
        $rc = new ReservaController(new Reserva($banco), $item, $banco);
        if ($caminho === '/api/itens' && $metodo === 'GET') {
            $ic->listar();
            return;
        }
        if (preg_match('#^/api/itens/(\d+)$#', $caminho, $m)) {
            if ($metodo === 'GET') {
                $ic->buscar((int)$m[1]);
                return;
            }
            if ($metodo === 'PUT') {
                $ic->atualizar((int)$m[1], $dados);
                return;
            }
            if ($metodo === 'DELETE') {
                $ic->excluir((int)$m[1]);
                return;
            }
        }
        if ($caminho === '/api/itens' && $metodo === 'POST') {
            $ic->criar($dados);
            return;
        }
        if ($caminho === '/api/reservas' && $metodo === 'GET') {
            $rc->listar();
            return;
        }
        if (preg_match('#^/api/reservas/(\d+)$#', $caminho, $m)) {
            if ($metodo === 'GET') {
                $rc->buscar((int)$m[1]);
                return;
            }
            if ($metodo === 'PUT') {
                $rc->atualizar((int)$m[1], $dados);
                return;
            }
            if ($metodo === 'DELETE') {
                $rc->excluir((int)$m[1]);
                return;
            }
        }
        if ($caminho === '/api/reservas' && $metodo === 'POST') {
            $rc->criar($dados);
            return;
        }
        responderErro('Rota não encontrada', 404);
    } catch (PDOException $e) {
        responderErro('Erro ao conectar ou acessar o banco de dados', 500);
    } catch (Throwable $e) {
        responderErro('Erro interno do servidor', 500);
    }
}
