<?php
require_once __DIR__ . '/../models/Item.php';
class ItemController
{
    public function __construct(private Item $item) {}
    private function resposta(mixed $d, int $s): void
    {
        http_response_code($s);
        header('Content-Type: application/json; charset=utf-8');
        if ($s !== 204) echo json_encode($d, JSON_UNESCAPED_UNICODE);
    }
    private function validar(array $d): true|string
    {
        foreach (['titulo', 'tipo', 'autor', 'ano'] as $c) if (!isset($d[$c]) || trim((string)$d[$c]) === '') return "O campo $c é obrigatório";
        if (!in_array($d['tipo'], ['Livro', 'Jogo'], true)) return 'O campo tipo deve ser Livro ou Jogo';
        if (!is_numeric($d['ano']) || (int)$d['ano'] < 0) return 'O campo ano deve ser um número válido';
        return true;
    }
    public function listar(): void
    {
        $this->resposta($this->item->listar(), 200);
    }
    public function buscar(int $id): void
    {
        $x = $this->item->buscar($id);
        $x ? $this->resposta($x, 200) : $this->resposta(['erro' => 'Item não encontrado'], 404);
    }
    public function criar(array $d): void
    {
        $v = $this->validar($d);
        if ($v !== true) {
            $this->resposta(['erro' => $v], 400);
            return;
        }
        $d = array_map('trim', $d);
        $d['ano'] = (int)$d['ano'];
        $id = $this->item->criar($d);
        $this->resposta(['mensagem' => 'Item cadastrado com sucesso', 'id' => $id], 201);
    }
    public function atualizar(int $id, array $d): void
    {
        $atual = $this->item->buscar($id);
        if (!$atual) {
            $this->resposta(['erro' => 'Item não encontrado'], 404);
            return;
        }
        $v = $this->validar($d);
        if ($v !== true) {
            $this->resposta(['erro' => $v], 400);
            return;
        }
        $d = array_map('trim', $d);
        $d['ano'] = (int)$d['ano'];
        if (array_key_exists('disponivel', $d) && !in_array($d['disponivel'], [0, 1, '0', '1'], true)) {
            $this->resposta(['erro' => 'O campo disponivel deve ser 0 ou 1'], 400);
            return;
        }
        $d['disponivel'] = array_key_exists('disponivel', $d) ? (int)$d['disponivel'] : (int)$atual['disponivel'];
        if ($d['disponivel'] === 1 && $this->item->possuiReservaAtiva($id)) {
            $this->resposta(['erro' => 'Um item com reserva ativa não pode ficar disponível'], 400);
            return;
        }
        $this->item->atualizar($id, $d);
        $this->resposta(['mensagem' => 'Item atualizado com sucesso'], 200);
    }
    public function excluir(int $id): void
    {
        if (!$this->item->buscar($id)) {
            $this->resposta(['erro' => 'Item não encontrado'], 404);
            return;
        }
        try {
            $this->item->excluir($id);
            $this->resposta(null, 204);
        } catch (PDOException $e) {
            $this->resposta(['erro' => 'Não foi possível excluir o item. Verifique se existem reservas relacionadas.'], 400);
        }
    }
}
