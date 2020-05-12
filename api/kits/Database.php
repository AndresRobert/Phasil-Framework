<?php

namespace Kits\Database;

use PDO;
use PDOException;
use Kits\Text;

abstract class MySQL {

    private static function open () {
        $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME;
        $connection = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    }

    private static function prepare_multiple_rows (array $rows): string {
        $multiple = [];
        foreach ($rows as $row) {
            $multiple[] = "('".implode("','", $row)."')";
        }
        return implode(",", $multiple);
    }

    private static function prepare_columns (array $columns, bool $brackets = FALSE): string {
        $_start_bracket = $brackets ? '(' : '';
        $_end_bracket = $brackets ? ')' : '';
        return $_start_bracket.implode(',', $columns).$_end_bracket;
    }

    private static function prepare_row (array $rows): string {
        return "(".implode("','", $rows).")";
    }

    private static function prepare_filter (array $filters): string {
        $filters = $filters + ['1' => '1'];
        return implode(' AND ', array_map(
            function ($columnName, $columnValue) {
                return sprintf("%s='%s'", $columnName, $columnValue);
            },
            array_keys($filters),
            array_values($filters)
        ));
    }

    private static function prepare_order (string $order): string {
        return $order === '' ? '' : ' ORDER BY '.$order;
    }

    private static function prepare_limit (string $limit): string {
        return $limit === '' ? '' : ' LIMIT '.$limit;
    }

    private static function prepare_update (array $columns): string {
        $_update = [];
        foreach ($columns as $column) {
            [$_column, $_value] = $column;
            $_update[] = $_column."='".$_value."'";
        }
        return implode(',', $_update);
    }

    private static function is_select_query (string $query): bool {
        $_query = strtolower($query);
        return Text::StartsWith('select', $_query);
    }

    public static function Fields (string $table): array {
        $_table = strtolower($table);
        $_query = "DESCRIBE {$_table}";
        try {
            $connection = self::open();
            $statement = $connection->prepare($_query);
            $statement->execute();
            $fields = $statement->fetchAll(PDO::FETCH_COLUMN);
            $connection = NULL;
        }
        catch (PDOException $e) {
            $fields = [];
        }
        return $fields;
    }

    public static function Insert (string $table, array $columns, array $rows, bool $multiple = FALSE): array {
        $_columns = self::prepare_columns($columns, TRUE);
        $_rows = $multiple
            ? self::prepare_multiple_rows($rows)
            : self::prepare_row($rows);
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
        $_columns = self::prepare_columns($columns);
        $_filters = self::prepare_filter($filters);
        $_order = self::prepare_order($order);
        $_limit = self::prepare_limit($limit);
        $_table = DB_TABLE_PREFIX.$table;
        $_query = "SELECT {$_columns} FROM {$_table} WHERE ({$_filters}) {$_order} {$_limit}";
        try {
            $connection = self::open();
            $rows = $connection->query($_query, PDO::FETCH_ASSOC)->fetchAll();
            $result = [
                'status' => 'success',
                'response' => $rows,
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
        $_columns = self::prepare_update($columns);
        $_filters = self::prepare_filter($filters);
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
        $_filters = self::prepare_filter($filters);
        $_table = DB_TABLE_PREFIX.$table;
        $_query = "DELETE FROM {$_table} WHERE {$_filters}";
        try {
            $connection = self::open();
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

    public static function ComplexSelect ($query) {
        $result = [
            'status' => 'fail',
            'response' => '-1',
            'error' => 'invalid query'
        ];
        if (self::is_select_query($query)) {
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
