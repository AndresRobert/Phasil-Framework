<?php

namespace Base;

use Kits\Database\MySQL;

/**
 * The force is strong with this Model class and you need to extend your classes from this one.
 * To actually get it to work you need to configure a Database doe...
 *
 * Class Model
 */
class Model {

    /**
     * Table name, you must redefine this in each constructor
     *
     * @var string
     */
    protected string $table = '';

    /**
     * Contains all the attributes of a particular model
     *
     * @var array
     */
    protected array $columns = [];

    /**
     * Contains all the column names of a particular model
     *
     * @var array
     */
    protected array $fields = [];

    /**
     * Gets an attribute by name of the current instance
     *
     * @param string $columnName
     *
     * @return mixed|null
     */
    public function get (string $columnName) {
        if (in_array($columnName, $this->fields)) {
            return $this->columns[0][$columnName] ?? NULL;
        }
        return NULL;
    }

    /**
     * Sets one or more attributes in the current instance ['key' => 'value']
     *
     * @param array $row
     */
    public function set (array $row): void {
        foreach ($row as $key => $value) {
            if (in_array($key, $this->fields)) {
                $this->columns[$key] = $value;
            }
        }
    }

    /**
     * You must define always the Table name before for each extended class
     *
     * Model constructor.
     */
    public function __construct () {
        $this->fields = MySQL::Fields($this->table);
    }

    /**
     * Model destructor
     */
    public function __destruct () { }

    /**
     * Inserts the current instance to the DB
     * If the ID is defined it will not be saved
     *
     * @return bool
     */
    public function create (): bool {
        if (is_null($this->get('id'))) {
            $result = MySQL::Insert(
                $this->table,
                array_keys($this->columns),
                array_values($this->columns)
            );
            if ($result['status'] === 'success') {
                $this->set(['id' => $result['response']]);
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Fill all attributes in the current instance by Id
     *
     * @return bool
     */
    public function read (): bool {
        if (!is_null($this->get('id'))) {
            $result = MySQL::Select(
                $this->table,
                ['*'],
                ['id' => $this->get('id')]
            );
            if ($result['status'] === 'success') {
                $this->set($result['response']);
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Fill all attributes in the current instance by filter
     *
     * @param array $filter
     *
     * @return bool
     */
    public function readBy (array $filter): bool {
        $result = MySQL::Select(
            $this->table,
            ['*'],
            $filter
        );
        if ($result['status'] === 'success') {
            $this->set($result['response']);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Updates the record by ID using current instance attributes
     *
     * @return bool
     */
    public function update (): bool {
        if (!is_null($this->get('id'))) {
            $result = MySQL::Update(
                $this->table,
                $this->columns,
                ['id' => $this->get('id')]
            );
            return $result['status'] === 'success';
        }
        return FALSE;
    }

    /**
     * Deletes the record by ID using current instance id
     *
     * @return bool
     */
    public function delete (): bool {
        if (!is_null($this->get('id'))) {
            $result = MySQL::Delete(
                $this->table,
                ['id' => $this->get('id')]
            );
            if ($result['status'] === 'success') {
                unset($this->columns['id']);
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Gets an array with all records that meet the filters
     *
     * @param array  $columns
     * @param array  $filters
     * @param string $order
     * @param string $limit
     *
     * @return array
     */
    public function filter (array $columns = ['*'], array $filters = [], string $order = '', string $limit = ''): array {
        $columnsAreValid  = !array_diff($columns, $this->fields) || $columns == ['*'];
        if ($columnsAreValid) {
            $result = MySQL::Select(
                $this->table,
                $columns,
                $filters,
                $order,
                $limit
            );
            if ($result['status'] === 'success') {
                return $result['response'];
            }
        }
        return [];
    }

    public function exists(string $column, string $value) {
        $columnsAreValid = !array_diff([$column], $this->fields);
        $rows = [];
        if ($columnsAreValid) {
            $rows = MySQL::Select(
                $this->table,
                ['id'],
                [$column => $value],
                'id',
                '1'
            );
        }
        return count($rows) > 0;
    }

}