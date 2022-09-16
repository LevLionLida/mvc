<?php

namespace Core\Traits\DB;

use Core\Db;
use Core\Model;
use PDO;

trait Queryable
{
    static protected string|null $tableName = "users";

    static protected string $query = "";

    protected array $columns = [];

    private string $command = '';
    private array $bindParams = [];


    public static function all(): static
    {
        static::$query = "SELECT * FROM " . static::$tableName;
        $obj = new static();
        $obj->commands[] = 'all';

        return $obj;
    }

    public function orderBy($column, $direction = 'ASC'): static
    {
        if ($this->allowMethod(['all', 'select'])) {
            $this->commands[] = 'order';
            static::$query .= " ORDER BY {$column} {$direction}";
        }
        return $this;
    }

    public static function find(int $id)
    {
        $query = "SELECT * FROM " . static::$tableName . " WHERE id=:id";

        $query = Db::connect()->prepare($query);
        $query->bindParam('id', $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchObject(static::class);
    }

    public static function findBy(string $column, $value)
    {
        $query = "SELECT * FROM " . static::$tableName . " WHERE {$column}=:{$column}";

        $query = Db::connect()->prepare($query);
        $query->bindParam($column, $value);
        $query->execute();

        return $query->fetchObject(static::class);
    }


    public function get()
    {
        return Db::connect()->query(static::$query)->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    protected function allowMethod(array $allowedMethods): bool
    {
        foreach ($allowedMethods as $method) {
            if (in_array($method, $this->commands)) {
                return true;
            }
        }
        return false;
    }


    public function getTableFields(): array// отобразить имена колонок таблицы
    {
        $query = "SHOW COLUMNS FROM " . static::$tableName;
        $result = Db::connect()->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $this->columns [] = $row['Field'];
        }
        return $this->columns;
    }

    public static function create(array $fields): string//записать данные в таблицу и вывести ид записи
    {
        $tableFields = (new self)->getTableFields();
        unset($tableFields[0]);
        array_walk($fields, function (&$value) {
            $value = "'$value'";
        });
        $query = "INSERT INTO " . static::$tableName . " (" . implode(', ', $tableFields) . ")
                          VALUES (" . implode(', ', ($fields)) . ")";
        $query = Db::connect()->prepare($query);
        $query->execute();
        $id = Db::connect()->lastInsertId();
        return $id;
    }

    public static function delete($id)
    {

        $query = " DELETE FROM " . static::$tableName . " WHERE `id` = $id";
        $query = Db::connect()->prepare($query);
        $query->execute();

    }

    public function update(array $fieldUpdate, $id)
    {
        $tableFields = (new self)->getTableFields();
        array_walk($fieldUpdate, function (&$value) {
            $value = "'$value'";
        });

        $query = " UPDATE " . static::$tableName . " SET  $tableFields[1] =$fieldUpdate[0], $tableFields[2] =$fieldUpdate[1],
        $tableFields[3] = $fieldUpdate[2] " . " WHERE `id` = $id";
        $query = Db::connect()->prepare($query);
        $query->execute();

    }

    public function select(string $columns = "*")
    {
        static::$query = "SELECT  {$columns}  FROM " . static::$tableName;
        return new static();

    }


    public function where(string $value, string $column, $condition)
    {
      if( stristr(static::$query,"SELECT")){

        static::$query .= " WHERE `$column` $condition $value";
        return $this;
      }
      else{
          throw new \Exception("Unable to execute method SELECT");
      }

    }


}
