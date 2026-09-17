<?php
require_once __DIR__ . '/../models/Reserva.php';
require_once __DIR__ . '/../models/Item.php';
class ReservaController
{
    public function __construct(private Reserva $reserva, private Item $item, private PDO $banco) {}
    private function resposta(mixed $d, int $s): void
    {
        http_response_code($s);
        header('Content-Type: application/json; charset=utf-8');
        if ($s !== 204) echo json_encode($d, JSON_UNESCAPED_UNICODE);
    }
    private function validar(array $d): true|string
    {
        foreach (['item_id', 'nome_usuario', 'data_reserva', 'status'] as $c) if (!isset($d[$c]) || trim((string)$d[$c]) === '') return "O campo $c é obrigatório";
        if (!is_numeric($d['item_id']) || (int)$d['item_id'] <= 0) return 'O campo item_id deve ser um número válido';
        if (!in_array($d['status'], ['ativa', 'cancelada'], true)) return 'O campo status deve ser ativa ou cancelada';
        $dt = DateTime::createFromFormat('Y-m-d', $d['data_reserva']);
        if (!$dt || $dt->format('Y-m-d') !== $d['data_reserva']) return 'A data_reserva deve estar no formato AAAA-MM-DD';
        return true;
    }
    public function listar(): void
    {
        $this->resposta($this->reserva->listar(), 200);
    }
    public function buscar(int $id): void
    {
        $x = $this->reserva->buscar($id);
        $x ? $this->resposta($x, 200) : $this->resposta(['erro' => 'Reserva não encontrada'], 404);
    }
    public function criar(array $d): void
    {
        $v = $this->validar($d);
        if ($v !== true) {
            $this->resposta(['erro' => $v], 400);
            return;
        }
        $d = array_map('trim', $d);
        $d['item_id'] = (int)$d['item_id'];
        $i = $this->item->buscar($d['item_id']);
        if (!$i) {
            $this->resposta(['erro' => 'Item não encontrado'], 404);
            return;
        }
        if ($d['status'] === 'ativa' && (int)$i['disponivel'] !== 1) {
            $this->resposta(['erro' => 'Este item não está disponível para reserva'], 400);
            return;
        }
        try {
            $this->banco->beginTransaction();
            $id = $this->reserva->criar($d);
            if ($d['status'] === 'ativa') $this->item->disponibilidade($d['item_id'], 0);
            $this->banco->commit();
            $this->resposta(['mensagem' => 'Reserva criada com sucesso', 'id' => $id], 201);
        } catch (PDOException $e) {
            if ($this->banco->inTransaction()) $this->banco->rollBack();
            $this->resposta(['erro' => 'Não foi possível criar a reserva'], 500);
        }
    }
    public function atualizar(int $id, array $d): void
    {
        $at = $this->reserva->buscarBruto($id);
        if (!$at) {
            $this->resposta(['erro' => 'Reserva não encontrada'], 404);
            return;
        }
        $v = $this->validar($d);
        if ($v !== true) {
            $this->resposta(['erro' => $v], 400);
            return;
        }
        $d = array_map('trim', $d);
        $d['item_id'] = (int)$d['item_id'];
        $novo = $this->item->buscar($d['item_id']);
        if (!$novo) {
            $this->resposta(['erro' => 'Item não encontrado'], 404);
            return;
        }
        $mudouItem = $d['item_id'] !== (int)$at['item_id'];
        $ocupaNovoItem = $d['status'] === 'ativa' && ($at['status'] !== 'ativa' || $mudouItem);
        if ($ocupaNovoItem && (int)$novo['disponivel'] !== 1) {
            $this->resposta(['erro' => 'O item não está disponível para reserva'], 400);
            return;
        }
        try {
            $this->banco->beginTransaction();
            $this->reserva->atualizar($id, $d);
            if ($mudouItem && $at['status'] === 'ativa') $this->item->disponibilidade((int)$at['item_id'], 1);
            if ($d['status'] === 'ativa') $this->item->disponibilidade($d['item_id'], 0);
            elseif ($at['status'] === 'ativa' && !$mudouItem) $this->item->disponibilidade($d['item_id'], 1);
            $this->banco->commit();
            $this->resposta(['mensagem' => 'Reserva atualizada com sucesso'], 200);
        } catch (PDOException $e) {
            if ($this->banco->inTransaction()) $this->banco->rollBack();
            $this->resposta(['erro' => 'Não foi possível atualizar a reserva'], 500);
        }
    }
    public function excluir(int $id): void
    {
        $r = $this->reserva->buscarBruto($id);
        if (!$r) {
            $this->resposta(['erro' => 'Reserva não encontrada'], 404);
            return;
        }
        try {
            $this->banco->beginTransaction();
            $this->reserva->excluir($id);
            if ($r['status'] === 'ativa') $this->item->disponibilidade((int)$r['item_id'], 1);
            $this->banco->commit();
            $this->resposta(null, 204);
        } catch (PDOException $e) {
            if ($this->banco->inTransaction()) $this->banco->rollBack();
            $this->resposta(['erro' => 'Não foi possível excluir a reserva'], 500);
        }
    }
}
