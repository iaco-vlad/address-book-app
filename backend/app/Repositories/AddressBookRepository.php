<?php

namespace AddressBook\Repositories;

use AddressBook\Models\AddressBook;
use AddressBook\Models\DTOs\Address;

class AddressBookRepository extends AbstractRepository
{
    public function getTable(): string
    {
        return 'address_book';
    }

    public function getModelClass(): string
    {
        return AddressBook::class;
    }

    public function search($searchTerm, $page = 1, $perPage = 10, $tagId = null): false|array
    {
        $offset = ($page - 1) * $perPage;

        $query = $this->db->prepare("
            SELECT address_book.*, cities.name as city_name 
            FROM {$this->getTable()} 
            LEFT JOIN cities ON address_book.city_id = cities.id 
            LEFT JOIN contacts_tags ct ON address_book.id = ct.contact_id
            WHERE (last_name LIKE ? OR first_name LIKE ? OR email LIKE ?)
            " . ($tagId ? "AND ct.tag_id = ?" : "") . "
            GROUP BY address_book.id
            LIMIT ?, ?
        ");
        $query->bindValue(1, "%$searchTerm%");
        $query->bindValue(2, "%$searchTerm%");
        $query->bindValue(3, "%$searchTerm%");

        if ($tagId) {
            $query->bindValue(4, $tagId);
            $query->bindValue(5, $offset, \PDO::PARAM_INT);
            $query->bindValue(6, $perPage, \PDO::PARAM_INT);
        } else {
            $query->bindValue(4, $offset, \PDO::PARAM_INT);
            $query->bindValue(5, $perPage, \PDO::PARAM_INT);
        }

        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS);
    }

    public function count($searchTerm = '', $tagId = null): int
    {
        $query = $this->db->prepare("
        SELECT COUNT(DISTINCT address_book.id)
        FROM {$this->getTable()}
        LEFT JOIN contacts_tags ct ON address_book.id = ct.contact_id
        WHERE (last_name LIKE ? OR first_name LIKE ? OR email LIKE ?)
        " . ($tagId ? "AND ct.tag_id = ?" : "")
        );

        $query->bindValue(1, "%$searchTerm%");
        $query->bindValue(2, "%$searchTerm%");
        $query->bindValue(3, "%$searchTerm%");

        if ($tagId) {
            $query->bindValue(4, $tagId);
        }

        $query->execute();

        return (int) $query->fetchColumn();
    }

    public function forExport(): false|array
    {
        $query = $this->db->prepare("
            SELECT address_book.last_name, address_book.first_name, address_book.email, address_book.street, address_book.zip_code, cities.name as city_name
            FROM {$this->getTable()}
            LEFT JOIN cities ON address_book.city_id = cities.id
        ");
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, Address::class);
    }
}