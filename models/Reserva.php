<?php
class Reserva
{
    public function __construct(private PDO $banco) {}
    public function listar(): array
    {
        return $this->banco->query('SELECT r.id,r.item_id,i.titulo AS item_titulo,r.nome_usuario,r.data_reserva,r.status FROM reservas r INNER JOIN itens i ON i.id=r.item_id ORDER BY r.id DESC')->fetchAll();
    }
    public function buscar(int $id): ?array
    {
        $s = $this->banco->prepare('SELECT r.id,r.item_id,i.titulo AS item_titulo,r.nome_usuario,r.data_reserva,r.status FROM reservas r INNER JOIN itens i ON i.id=r.item_id WHERE r.id=?');
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }
    public function buscarBruto(int $id): ?array
    {
        $s = $this->banco->prepare('SELECT * FROM reservas WHERE id=?');
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }
    public function criar(array $d): int
    {
        $s = $this->banco->prepare('INSERT INTO reservas(item_id,nome_usuario,data_reserva,status) VALUES(?,?,?,?)');
        $s->execute([$d['item_id'], $d['nome_usuario'], $d['data_reserva'], $d['status']]);
        return (int)$this->banco->lastInsertId();
    }
    public function atualizar(int $id, array $d): bool
    {
        $s = $this->banco->prepare('UPDATE reservas SET item_id=?,nome_usuario=?,data_reserva=?,status=? WHERE id=?');
        return $s->execute([$d['item_id'], $d['nome_usuario'], $d['data_reserva'], $d['status'], $id]);
    }
    public function excluir(int $id): bool
    {
        $s = $this->banco->prepare('DELETE FROM reservas WHERE id=?');
        return $s->execute([$id]);
    }
}
