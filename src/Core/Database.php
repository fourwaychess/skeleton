<?php declare(strict_types=1);
/**
 * GNU Affero General Public License v3.0
 * 
 * Copyright (c) 2021 Four Way Chess
 * 
 * Permissions of this strongest copyleft license are conditioned on making available complete source code
 * of licensed works and modifications, which include larger works using a licensed work, under the same license.
 * Copyright and license notices must be preserved. Contributors provide an express grant of patent rights.
 * When a modified version is used to provide a service over a network, the complete source code of the
 * modified version must be made available.
 */

namespace FourWayChess\Core;

use PDO;

/**
 * A secure by defualt database abstraction.
 */
class Database implements DatabaseInterface
{
    /**
     * Construct a new database handler.
     *
     * @param \FourWayChess\Core\ConnectionInterface $connection The database connection.
     *
     * @return void Returns nothing.
     */
    public function __construct(public ConnectionInterface $connection)
    {
        //
    }

    /**
     * Select and retrive data from the database.
     *
     * @param string $sql       The sql statement to execute.
     * @param array  $array     The where data.
     * @param int    $fetchMode The type of pdo behaviour to apply.
     *
     * @return array Returns an array of records.
     */
    public function select(string $sql, array $array = array(), int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $sth = $this->connection->prepare($sql);
        foreach ($array as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        $sth->execute();
        return $sth->fetchAll($fetchMode);
    }

    /**
     * Insert data into the database.
     *
     * @param string $table The table where we are updating.
     * @param array  $data  The data where keys to insert.
     *
     * @return void Returns nothing.
     */
    public function insert(string $table, array $data): void
    {
        ksort($data);
        $fieldNames = implode(', ', array_keys($data));
        $fieldValues = ':' . implode(', :', array_keys($data));
        $sth = $this->connection->prepare("INSERT INTO $table ($fieldNames) VALUES ($fieldValues)");
        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        $sth->execute();
    }

    /**
     * Preform a database update.
     *
     * @param string $table The table where we are updating.
     * @param array  $data  The data where keys to insert.
     * @param string $where The where bind string.
     * @param array  $bind  The bind array values to insert.
     *
     * @return void Returns nothing.
     */
    public function update(string $table, array $data, string $where, array $bind = array()): void
    {
        ksort($data);
        $fieldDetails = "";
        foreach ($data as $key => $value) {
            $fieldDetails .= "$key=:$key,";
        }
        $details = rtrim($fieldDetails, ',');
        $sth = $this->connection->prepare("UPDATE $table SET $fieldDetails WHERE $where");
        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        foreach ($bind as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        $sth->execute();
    }

    /**
     * Preform a database delete.
     *
     * @param string $table The table name to access.
     * @param string $where The where statement.
     * @param array  $bind  The bind data.
     * @param int    $limit The maximum amount of records to delete.
     *
     * @return void Returns nothing.
     */
    public function delete(string $table, string $where, array $bind = array(), int $limit = null): void
    {
        $query = "DELETE FROM $table WHERE $where";
        if ($limit) {
            $query .= " LIMIT $limit";
        }
        $sth = $this->connection->prepare($query);
        foreach ($bind as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        $sth->execute();
    }
}
