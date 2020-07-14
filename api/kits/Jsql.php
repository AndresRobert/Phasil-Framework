<?php

namespace Kits;

class Jsql {
    
    private string $table;

    public function __construct(string $tableName) {
        $this->table = JSQL_FOLDER.$tableName;
        $this->create();
    }

    /**
     * Creates the file as a table to be used (if does not exists)
     *
     * @return bool
     */
    private function create(): bool {
        return File::Create($this->table);
    }

    /**
     * Converts the full file into an array and returns:
     * records => [0 => ['id' => 1231231, 'name' => 'ann', ...], 1 => ['id' => 54362453, 'name' => 'ben', ...], ...]
     * by_id => [1231231 => 0, 54362453 => 1, ...]
     * by_key => [0 => 1231231, 1 => 54362453, ...]
     *
     * @return array
     */
    private function selectAll(): array {
        $fileContent = File::Read($this->table);
        $allRecords = Toolbox::JsonToArray($fileContent);
        $keys = $ids = [];
        foreach ($allRecords as $key => $record) {
            $keys[$record['id']] = $key;
            $ids[$key] = $record['id'];
        }
        return ['records' => $allRecords, 'by_id' => $keys, 'by_key' => $ids];
    }

    /**
     * Filters records as matching conditions and fields
     *
     * @param array $records
     * @param array $conditions
     * @param array $fields
     * @return array
     */
    private function filterRecords(array $records, array $conditions, array $fields): array {
        $filtered = [];
        foreach ($records as $record) {
            if ($this->match($conditions, $record)) {
                $filtered[] = $this->filterFields($record, $fields);
            }
        }
        return $filtered;
    }

    /**
     * Truncates records to match the fields, if no value for a field it returns ''
     *
     * @param array $record
     * @param array $fields
     * @return array
     */
    private function filterFields(array $record, array $fields): array {
        $filtered = [];
        foreach ($fields as $field) {
            $filtered[$field] = $record[$field] ?? '';
        }
        return $filtered;
    }

    /**
     * Returns whether the record matches a set of conditions or not
     *
     * @param array $conditions
     * @param array $record
     * @return bool
     */
    private function match(array $conditions, array $record): bool {
        foreach ($conditions as $field => $value) {
            if ($record[$field] !== $value) {
                return false;
            }
        }
        return true;
    }

    /**
     * Drops the table by removing the file
     *
     * @return bool
     */
    public function drop(): bool {
        return File::Delete($this->table);
    }

    /**
     * Truncates the table by removing the file contents
     *
     * @return bool
     */
    public function truncate(): bool {
        return File::Clear($this->table);
    }

    /**
     * Adds one or more records on the table by appending them on the file
     * Note: all records will be added
     *
     * @param array $records
     * @return int
     */
    public function insert(array $records): int {
        if (count($records) > 0) {
            foreach ($records as $key => $record) {
                $records[$key]['id'] = time();
            }
            File::Write($this->table, Toolbox::ArrayToJson($records));
            return count($records);
        }
        return 0;
    }

    /**
     * Returns an array with the records matching a set of conditions and fields
     *
     * @param array $fields
     * @param array $conditions
     * @return array
     */
    public function select(array $fields, array $conditions): array {
        return $this->filterRecords($this->selectAll()['records'], $conditions, $fields);
    }

    /**
     * Updates one or more records by replacing the content on the file
     * Note: the records must provide an ID field and it must match a record in the file
     *
     * @param array $records
     * @return int
     */
    public function replace(array $records): int {
        $query = $this->selectAll();
        $allRecords = $query['records'];
        $allIds = $query['by_id'];
        $count = 0;
        foreach ($records as $record) {
            if (isset($allIds[$record['id']])) {
                $allRecords[$allIds[$record['id']]] = $record;
                $count++;
            }
        }
        File::Write($this->table, Toolbox::ArrayToJson($allRecords), 'replace');
        return $count;
    }

    /**
     * Deletes one or more record by replacing the content on the file
     *
     * @param array $ids
     * @return int
     */
    public function delete(array $ids): int {
        $query = $this->selectAll();
        $allRecords = $query['records'];
        $allIds = $query['by_id'];
        $count = 0;
        foreach ($ids as $id) {
            if (isset($allIds[$id])) {
                unset($allRecords[$allIds[$id]]);
                $count++;
            }
        }
        File::Write($this->table, Toolbox::ArrayToJson($allRecords), 'replace');
        return $count;
    }

}