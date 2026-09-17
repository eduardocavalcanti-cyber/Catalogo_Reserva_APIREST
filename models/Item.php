<?php
class Item
{
    public function __construct(private PDO $banco) {}
    public function listar(): array
    {
        return $this->banco->query('SELECT id,titulo,tipo,autor,ano,disponivel FROM itens ORDER BY id DESC')->fetchAll();
    }
    public function buscar(int $id): ?array
    {
        $s = $this->banco->prepare('SELECT id,titulo,tipo,autor,ano,disponivel FROM itens WHERE id=?');
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }
    public function criar(array $d): int
    {
        $s = $this->banco->prepare('INSERT INTO itens(titulo,tipo,autor,ano,disponivel) VALUES(?,?,?,?,1)');
        $s->execute([$d['titulo'], $d['tipo'], $d['autor'], $d['ano']]);
        return (int)$this->banco->lastInsertId();
    }
    public function atualizar(int $id, array $d): bool
    {
        $s = $this->banco->prepare('UPDATE itens SET titulo=?,tipo=?,autor=?,ano=?,disponivel=? WHERE id=?');
        return $s->execute([$d['titulo'], $d['tipo'], $d['autor'], $d['ano'], $d['disponivel'], $id]);
    }
    public function possuiReservaAtiva(int $id): bool
    {
        $s = $this->banco->prepare("SELECT 1 FROM reservas WHERE item_id=? AND status='ativa' LIMIT 1");
        $s->execute([$id]);
        return (bool)$s->fetchColumn();
    }
    public function excluir(int $id): bool
    {
        $s = $this->banco->prepare('DELETE FROM itens WHERE id=?');
        return $s->execute([$id]);
    }
    public function disponibilidade(int $id, int $valor): bool
    {
        $s = $this->banco->prepare('UPDATE itens SET disponivel=? WHERE id=?');
        return $s->execute([$valor, $id]);
    }
}
