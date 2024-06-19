<?php

namespace AddressBook\Repositories;

use PDO;

abstract class AbstractRepository
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    abstract public function getTable();

    abstract public function getModelClass();

    public function all(): array
    {
        $query = $this->db->query("SELECT * FROM {$this->getTable()}");
        return $query->fetchAll(PDO::FETCH_CLASS, $this->getModelClass());
    }

    public function find($id): mixed
    {
        $query = $this->db->prepare("SELECT * FROM {$this->getTable()} WHERE id = ?");
        $query->execute([$id]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->getModelClass());
        return $query->fetch();
    }

    public function create($data, $noId = false): mixed
    {
        if (is_object($data)) {
            $data = $data->toArray();
        }
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $query = $this->db->prepare("INSERT INTO {$this->getTable()} ($columns) VALUES ($placeholders)");
        $query->execute(array_values($data));

        return $noId
            ? true
            : $this->find($this->db->lastInsertId());
    }

    public function update($id, $data): mixed
    {
        if (is_object($data)) {
            $data = $data->toArray();
        }
        $columns = implode(' = ?, ', array_keys($data)) . ' = ?';

        $query = $this->db->prepare("UPDATE {$this->getTable()} SET $columns WHERE id = ?");
        $query->execute(array_merge(array_values($data), [$id]));

        return $this->find($id);
    }

    public function delete($id): void
    {
        $query = $this->db->prepare("DELETE FROM {$this->getTable()} WHERE id = ?");
        $query->execute([$id]);
    }

    public function count(): int
    {
        $query = $this->db->query("SELECT COUNT(*) FROM {$this->getTable()}");
        return (int) $query->fetchColumn();
    }
}