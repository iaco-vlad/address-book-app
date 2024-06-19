<?php

namespace AddressBook\Repositories;

use AddressBook\Models\ContactTag;

class ContactTagRepository extends AbstractRepository
{
    public function getTable(): string
    {
        return 'contacts_tags';
    }

    public function getModelClass(): string
    {
        return ContactTag::class;
    }

    public function deleteByContactId(int $id): void
    {
        $query = $this->db->prepare("
            DELETE FROM {$this->getTable()}
            WHERE contact_id = ?
        ");
        $query->execute([$id]);
    }

    public function getTagsByContactId(mixed $id): array
    {
        $query = $this->db->prepare("
            SELECT ct.tag_id
            FROM {$this->getTable()} ct
            WHERE ct.contact_id = ?
        ");
        $query->execute([$id]);

        return $query->fetchAll(\PDO::FETCH_COLUMN);
    }
}