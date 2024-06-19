<?php

namespace AddressBook\Repositories;

use AddressBook\Models\Tag;

class TagRepository extends AbstractRepository
{
    public function getTable(): string
    {
        return 'tags';
    }

    public function getModelClass(): string
    {
        return Tag::class;
    }

    public function getActive(): array
    {
        $query = $this->db->query("
            SELECT t.*
            FROM {$this->getTable()} t
            INNER JOIN contacts_tags ct ON t.id = ct.tag_id
            GROUP BY t.id
        ");
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->getModelClass());
    }
}