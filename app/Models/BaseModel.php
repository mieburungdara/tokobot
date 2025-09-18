<?php

namespace TokoBot\Models;

use PDO;
use TokoBot\Helpers\Database;
use TokoBot\Exceptions\DatabaseException;

abstract class BaseModel
{
    protected PDO $db;
    protected string $table;
    private static ?PDO $pdo_instance = null;

    public function __construct()
    {
        $this->db = self::getDb();
        // Table name should be set in the child class, or derived from class name
        if (empty($this->table)) {
            $this->table = $this->deriveTableName();
        }
    }

    /**
     * Get the cached PDO instance.
     *
     * @return PDO
     */
    protected static function getDb(): PDO
    {
        if (self::$pdo_instance === null) {
            self::$pdo_instance = Database::getInstance();
        }
        return self::$pdo_instance;
    }

    /**
     * Derives table name from class name (e.g., UserModel -> users)
     *
     * @return string
     */
    protected function deriveTableName(): string
    {
        $className = (new \ReflectionClass($this))->getShortName();
        // Remove 'Model' suffix and convert to snake_case, then pluralize
        $tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace('Model', '', $className)));
        // Simple pluralization (can be improved for irregular plurals)
        if (substr($tableName, -1) === 's') {
            return $tableName;
        }
        return $tableName . 's';
    }

    /**
     * Find a record by its ID.
     *
     * @param int $id
     * @return array|false
     * @throws DatabaseException
     */
    public function find(int $id): array|false
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Error finding record in {$this->table}: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Get all records from the table.
     *
     * @return array
     * @throws DatabaseException
     */
    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table}");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Error fetching all records from {$this->table}: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Create a new record.
     *
     * @param array $data
     * @return int|false Inserted ID or false on failure
     * @throws DatabaseException
     */
    public function create(array $data): int|false
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->execute();
            return (int)$this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new DatabaseException("Error creating record in {$this->table}: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Update an existing record.
     *
     * @param int $id
     * @param array $data
     * @return int Number of affected rows
     * @throws DatabaseException
     */
    public function update(int $id, array $data): int
    {
        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setClauses);

        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET {$setClause} WHERE id = :id");
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new DatabaseException("Error updating record in {$this->table}: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Delete a record.
     *
     * @param int $id
     * @return int Number of affected rows
     * @throws DatabaseException
     */
    public function delete(int $id): int
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new DatabaseException("Error deleting record from {$this->table}: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
