<?php
namespace Ciebit\Photos\Helpers;

use PDOStatement;

use function array_map;
use function count;
use function implode;

abstract class Sql
{
    static private $counterKey = 0;

    private $limit; #: int
    private $offset; #: int
    private $sqlBindList; #: array
    private $sqlFilters; #: array
    private $sqlJoin; #: array
    private $sqlOrderBy; #: array

    public function __construct()
    {
        $this->sqlBindList = [];
        $this->sqlFilters = [];
        $this->sqlLimit = [];
        $this->sqlJoin = [];
        $this->sqlOrderBy = [];
    }

    protected function addSqlBind(string $key, int $type, $value): self
    {
        $this->sqlBindList[] = [
            'key' => $key,
            'value' => $value,
            'type' => $type
        ];
        return $this;
    }

    protected function addSqlFilter(string $sql): self
    {
        $this->sqlFilters[] = $sql;
        return $this;
    }

    protected function addSqlOrderBy(string $column, string $order): self
    {
        $this->sqlOrderBy[] = [$column, $order];
        return $this;
    }

    protected function addSqlParam(string $column, string $operator, array $values): self
    {
        $keys = [];

        foreach ($values as $value) {
            $key = 'param' . self::$counterKey++;
            $this->addSqlBind($key, PDO::PARAM_INT, $value);
            $keys[] = ":{$key}";
        }

        $keysStr = implode(',', $keys);
        if (count($values) > 1) {
            $keysStr = "({$keyStr})";
        }

        $this->addSqlFilter("{$column} {$operator} {$keysStr}");
    }

    protected function bind(PDOStatement $statment): self
    {
        if (! is_array($this->sqlBindList)) {
            return $this;
        }
        foreach ($this->sqlBindList as $bind) {
            $statment->bindValue(":{$bind['key']}", $bind['value'], $bind['type']);
        }
        return $this;
    }

    protected function generateSqlFilters(): string
    {
        if (empty($this->sqlFilters)) {
            return '1';
        }
        return implode(' AND ', $this->sqlFilters);
    }

    protected function generateSqlLimit(): string
    {
        $init = (int) $this->offset;
        $sql =
            $this->limit === null
            ? ''
            : "LIMIT {$init},{$this->limit}";
        return $sql;
    }

    protected function generateSqlJoin(): string
    {
        return implode(' ', $this->sqlJoin);
    }

    protected function generateSqlOrder(): string
    {
        if (empty($this->sqlOrderBy)) {
            return '';
        }

        $orderCommands = array_map(
            function($item) { return implode(" ", $item); },
            $this->sqlOrderBy
        );

        $sql = "ORDER BY " . implode(', ', $orderCommands);
        return $sql;
    }

    protected function setSqlLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    protected function setSqlOffset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }
}
