<?php

namespace Kits\Database;

use PDO;
use PDOException;
use Kits\Text;

abstract class MySQL {

    private static function Open (): PDO
    {
        $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME;
        $connection = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    }

    private static function PrepareMultipleRows (array $rows): string {
        $multiple = [];
        foreach ($rows as $row) {
            $multiple[] = "('".implode("','", $row)."')";
        }
        return implode(",", $multiple);
    }

    private static function PrepareColumns (array $columns, bool $brackets = FALSE): string {
        $_start_bracket = $brackets ? '(' : '';
        $_end_bracket = $brackets ? ')' : '';
        return $_start_bracket.implode(',', $columns).$_end_bracket;
    }

    private static function PrepareRow (array $rows): string {
        return "('".implode("','", $rows)."')";
    }

    private static function PrepareFilter (array $filters): string {
        $filters = ['1' => '1'] + $filters;
        return implode(' AND ', array_map(
            static function ($columnName, $columnValue) {
                return sprintf("%s='%s'", $columnName, $columnValue);
            },
            array_keys($filters),
            array_values($filters)
        ));
    }

    private static function PrepareOrder (string $order): string {
        return $order === '' ? '' : ' ORDER BY '.$order;
    }

    private static function PrepareLimit (string $limit): string {
        return $limit === '' ? '' : ' LIMIT '.$limit;
    }

    private static function PrepareUpdate (array $columns): string {
        return implode(',', array_map(
            static function ($columnName, $columnValue) {
                return sprintf("%s='%s'", $columnName, $columnValue);
            },
            array_keys($columns),
            array_values($columns)
        ));
    }

    private static function IsSelectQuery (string $query): bool {
        $_query = strtolower($query);
        return Text::StartsWith('select', $_query);
    }

    public static function Check (): ?array
    {
        try {
            self::open();
            return ['status' => 'success', 'message' => 'Connected to database'];
        }
        catch (PDOException $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }
    }

    public static function Fields (string $table): array {
        $_table = strtolower($table);
        $_query = "DESCRIBE {$_table}";
        try {
            $connection = self::open();
            $statement = $connection->query($_query);
            $fields = $statement->fetchAll(PDO::FETCH_COLUMN);
            $connection = NULL;
        }
        catch (PDOException $e) {
            $fields = [];
        }
        return $fields;
    }

    public static function Insert (string $table, array $columns, array $rows, bool $multiple = FALSE): array {
        $_columns = self::PrepareColumns($columns, TRUE);
        $_rows = $multiple
            ? self::PrepareMultipleRows($rows)
            : self::PrepareRow($rows);
        $_table = DB_TABLE_PREFIX.$table;
        $_query = "INSERT INTO {$_table} {$_columns} VALUES {$_rows}";
        try {
            $connection = self::open();
            $inserted = $connection->exec($_query);
            if ($inserted > 0) {
                $result = [
                    'status' => 'success',
                    'response' => $connection->lastInsertId(),
                    'error' => 'no error'
                ];
            }
            else {
                $result = [
                    'status' => 'fail',
                    'response' => -1,
                    'error' => $inserted.' rows inserted'
                ];
            }
            $connection = NULL;
        }
        catch (PDOException $e) {
            $result = [
                'status' => 'fail',
                'response' => -1,
                'error' => $e->getMessage(),
            ];
        }
        return $result + ['statement' => $_query];
    }

    public static function Select (string $table, array $columns, array $filters = [], string $order = '', string $limit = ''): array {
        $_columns = self::PrepareColumns($columns);
        $_filters = self::PrepareFilter($filters);
        $_order = self::PrepareOrder($order);
        $_limit = self::PrepareLimit($limit);
        $_table = DB_TABLE_PREFIX.$table;
        $_query = "SELECT {$_columns} FROM {$_table} WHERE ({$_filters}) {$_order} {$_limit}";
        try {
            $connection = self::Open();
            $rows = $connection->query($_query, PDO::FETCH_ASSOC)->fetchAll();
            $result = [
                'status' => 'success',
                'response' => $rows[0],
                'error' => 'no error'
            ];
            $connection = NULL;
        }
        catch (PDOException $e) {
            $result = [
                'status' => 'fail',
                'response' => '-1',
                'error' => $e->getMessage(),
            ];
        }
        return $result + ['statement' => $_query];
    }

    public static function Update (string $table, array $columns, array $filters): array {
        $_columns = self::PrepareUpdate($columns);
        $_filters = self::PrepareFilter($filters);
        $_table = DB_TABLE_PREFIX.$table;
        $_query = "UPDATE {$_table} SET {$_columns} WHERE {$_filters}";
        try {
            $connection = self::open();
            $updated = $connection->exec($_query);
            $result = [
                'status' => 'success',
                'response' => $updated,
                'error' => 'no error'
            ];
            $connection = NULL;
        }
        catch (PDOException $e) {
            $result = [
                'status' => 'fail',
                'response' => -1,
                'error' => $e->getMessage(),
            ];
        }
        return $result + ['statement' => $_query];
    }

    public static function Delete (string $table, array $filters): array {
        $_filters = self::PrepareFilter($filters);
        $_table = DB_TABLE_PREFIX.$table;
        $_query = "DELETE FROM {$_table} WHERE {$_filters}";
        try {
            $connection = self::Open();
            $deleted = $connection->exec($_query);
            $result = [
                'status' => 'success',
                'response' => $deleted,
                'error' => 'no error'
            ];
            $connection = NULL;
        }
        catch (PDOException $e) {
            $result = [
                'status' => 'fail',
                'response' => -1,
                'error' => $e->getMessage(),
            ];
        }
        return $result + ['statement' => $_query];
    }

    public static function ComplexSelect ($query): array
    {
        $result = [
            'status' => 'fail',
            'response' => '-1',
            'error' => 'invalid query'
        ];
        if (self::IsSelectQuery($query)) {
            try {
                $connection = self::open();
                $rows = $connection->query($query, PDO::FETCH_ASSOC)->fetchAll();
                $result = [
                    'status' => 'success',
                    'response' => $rows,
                    'error' => 'no error'
                ];
                $connection = NULL;
            }
            catch (PDOException $e) {
                $result['error'] = $e->getMessage();
            }
        }
        return $result + ['statement' => $query];
    }

}
