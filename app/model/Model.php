<?php

namespace App\Model;

use App\Classes\Db;

abstract class Model {

	protected $table;

	protected $primaryKey = 'id';

	protected $keyType = 'int';

	protected $incrementing = true;

	protected $perPage = 20;

	protected $exists = false;

	protected $sort = 'id';

	const CREATED_AT = 'created_at';

	const UPDATED_AT = 'updated_at';

	private $attributes = [];

	public function __construct()
	{
		// Fill in table name
		if (empty($this->table)) {
			$class = strtolower(get_class($this));
			$pos = strrpos($class, '\\');
			$this->table = substr($class, $pos + 1);
		}

		// Pull columns from cache
		$columnsCache = Db::getColumns();

		// If exists in cache
		if (array_key_exists($this->table, $columnsCache)) {
			$columns = $columnsCache[$this->table];
		}
		// Otherwise query Db and add to cache
		else {
			$columns = self::query("SHOW COLUMNS FROM `$this->table`");
			$columns = array_column($columns, 'Field');
			$columnsCache[$this->table] = $columns;
			Db::setColumns($columnsCache);
		}

		// Create attribute placeholders
		if (_exists($columns)) {
			foreach ($columns as $column) {
				if ($column != $this->primaryKey) {
					$this->attributes[] = $column;
				}
				$this->{$column} = null;
			}
		}
	}

	//-------------------------------------//

	/**
	 * Retreive data via PDO prepared statements
	 *
	 * The most frequently used constants for PDO are listed below,
	 * find more at: {@link https://www.php.net/manual/en/pdo.constants.php}
	 *	- PDO::PARAM_BOOL
	 *	- PDO::PARAM_NULL
	 *	- PDO::PARAM_INT
	 *	- PDO::PARAM_STR
	 *
	 * Usage:
	 * self::query(
	 *     "SELECT * FROM `example` WHERE `id` = :id AND `number` = :number AND `text` = :text", [
	 *         [':id', 1],
	 *         [':number', 7, \PDO::PARAM_INT],
	 *         [':text', 'A random string', \PDO::PARAM_STR],
	 *     ]);
	 *
	 * self::query(
	 *     'SELECT * FROM `example` WHERE `id` IN (?, ?, ?) AND `thing` = ?, [
	 *         1, 2, 3, 'stuff'
	 *     ],
	 *     '?'
	 * );
	 *
	 * @param $query The full prepared query statement
	 * @param $parameters The values to insert into the prepared statement
	 * @param $type Type of prepared statement, ':' for named placeholders,
     *                                          '?' for value placeholders
	 *
	 * @return array|null Retreived data, or null
	 */
	protected static function query(string $query, array $parameters = [],
		$type = ':'): ?array
	{
		if (substr_count($query, $type) != count($parameters)) {
			return null;
		}

		$query = Db::get()->prepare($query);

		$success = false;
		if ($type == '?') {
			$success = $query->execute($parameters);
		}
		else {
			foreach ($parameters as $key => $parameter) {
				if (count($parameter) == 2) {
					$query->bindParam($parameter[0], $parameter[1]);
				}
				else if (count($parameter) == 3) {
					$query->bindParam($parameter[0], $parameter[1], $parameter[2]);
				}
			}
			$success = $query->execute();
		}

		if (!$success) {
			return null;
		}

		return $query->fetchAll();
	}

	//-------------------------------------//

	public function exists(): bool
	{
		if ($this->exists == true) {
			return $this->exists;
		}

		$exists = self::query(
			"SELECT id FROM `$this->table` WHERE `$this->primaryKey` = :id", [
				[':id', $this->{$this->primaryKey}],
			]
		);

		if (_exists($exists)) {
			$this->exists = true;
		}

		return $this->exists;
	}

	public function save(): bool
	{
		$parameters = [];

		$exists = $this->exists();
		// Insert new Model
		if (!$exists) {
			$query = "INSERT INTO `$this->table` ";
			for ($i = 0; $i < 2; $i++) {
				if ($i == 0) {
					$query .= '(';
				}
				else {
					$query .= 'VALUES (';
				}

				foreach ($this->attributes as $key => $attribute) {
					if ($key != 0) {
						$query .= ', ';
					}

					if ($i == 0) {
						$query .= "`$attribute`";
					}
					else {
						$query .= ":$attribute";
						$parameters[] = [":$attribute", $this->{$attribute}];
					}
				}
				$query .= ') ';
			}
		}
		// Update existing Model
		else {
			$query = "UPDATE `$this->table` SET ";
			foreach ($this->attributes as $key => $attribute) {
				if ($key != 0) {
					$query .= ', ';
				}

				$query .= "`$attribute` = :$attribute";
				$parameters[] = [":$attribute", $this->{$attribute}];
			}
			$query .= " WHERE `$this->primaryKey` = :id";
			$parameters[] = [':id', $this->{$this->primaryKey}];
		}

		if (self::query($query, $parameters) === null) {
			return false;
		}

		// Fill in primary key and exists for newly created Models
		if (!$exists) {
			$id = self::query("SELECT LAST_INSERT_ID() as `$this->primaryKey`");
			if (_exists($id)) {
				$this->{$this->primaryKey} = $id[0][$this->primaryKey];
				$this->exists = true;
			}
		}

		return true;
	}

	public function delete(): bool
	{
		return self::query(
			"DELETE FROM `$this->table` WHERE `$this->primaryKey` = :id", [
				[':id', $this->{$this->primaryKey}],
			]
		) !== null;
	}

	public function count(): int
	{
		$total = self::query(
			"SELECT COUNT({$this->primaryKey}) as total FROM `$this->table`");
		return $total[0]['total'] ?? 0;
	}

	//-------------------------------------//

	public function fill(array $fill): bool
	{
		if (!_exists([$fill])) {
			return false;
		}

		// Set primary key
		if (_exists($fill, $this->primaryKey)) {
			$this->{$this->primaryKey} = $fill[$this->primaryKey];
		}

		// Set other attributes
		foreach ($this->getAttributes() as $attribute) {
			if (isset($fill[$attribute])) {
				// Escape sequences are only interpreted with double quotes!
				$this->{$attribute} = preg_replace('/\r\n?/', "\n", $fill[$attribute]);
			}
		}

		return true;
	}

	public function validate(): bool
	{
		foreach ($this->getAttributes() as $attribute) {

			$required = false;
			foreach ($this->rules as $rule) {
				if ($rule[0] == $attribute && $rule[2] == 1 && $rule[3] == 0) {
					$required = true;
					break;
				}
			}

			// Exit if rule is marked 'required' but empty, "0" is not empty!
			if ($required && empty($this->{$attribute}) && $this->{$attribute} !== "0") {
				return false;
			}
		}

		return true;
	}

	public function getPrimaryKey(): string
	{
		return $this->primaryKey;
	}

	public function getAttributes(): array
	{
		return $this->attributes;
	}

	public function getAttributesRules(): array
	{

		// Check if model has set rules
		if (!_exists($this->rules) || !is_array($this->rules)) {
			$rules = [];
		}
		else {
			$rules = $this->rules;
		}

		// Get first column (name) of every rule
		$rulesSearch = array_column($rules, 0);

		// Loop through attributes
		foreach ($this->attributes as $attribute) {
			$found = array_search($attribute, $rulesSearch);

			if ($found === false) {
				// Define default ruleset
				$rules[] = [$attribute, "text", 0, 0];
				// Name | Type | Required | Filtered
				// Type can be:
				// - text
				// - textarea
				// - checkbox
				// - dropdown // @Todo: store dropdown data
			}
		}

		return $rules;
	}

	// Placeholder for generating the dropdown data
	public function getDropdownData(string $type): array
	{
		return [];
	}

	public function getSort(): string
	{
		return is_array($this->sort)
			? '`' . implode('`, `', $this->sort) . '`'
			: '`' . $this->sort . '`';
	}

	//-------------------------------------//

	public static function select(string $select = '*', string $filter = '',
		array $parameters = [], $type = ':'): Model
	{
		$class = get_called_class();
		$model = new $class;

		$select = self::query("SELECT $select FROM `$model->table` $filter",
			$parameters, $type);
		if (_exists($select)) {
			$model->fill($select[0]);
		}

		return $model;
	}

	public static function selectAll(string $select = '*', string $filter = '',
		array $parameters = [], $type = ':'): ?array
	{
		$class = get_called_class();
		$model = new $class;

		return self::query("SELECT $select FROM `$model->table` $filter",
			$parameters, $type);
	}

	public static function find(int $id): Model
	{
		$class = get_called_class();
		$model = new $class;

		$find = self::query("SELECT * FROM `$model->table` WHERE `$model->primaryKey` = :id", [
			[':id', $id],
		]);
		if (_exists($find)) {
			$model->fill($find[0]);
		}

		return $model;
	}

	// @Todo: add field name to query -> update Media.php md5
	public static function findAll(array $id = []): ?array
	{
		if (_exists($id)) {
			$id = array_values(array_unique($id));
			$in = str_repeat('?, ', count($id) - 1) . '?';
			array_push($id, ...$id);
			return self::selectAll(
				'*', "WHERE id IN ($in) ORDER BY FIELD(id, $in)", $id, '?');
		}
		else {
			return self::selectAll();
		}
	}

	public static function findOrFail(int $id): Model
	{
		$model = self::find($id);
		if (!$model->exists()) {
			throw new \Exception('Could not find Model!');
		}

		return $model;
	}

	public static function search(array $constraints, string $delimiter = 'AND'): Model
	{
		$parameters = [];

		$filter = "WHERE ";
		foreach (array_keys($constraints) as $key => $constraint) {
			if ($key != 0) {
				$filter .= " $delimiter ";
			}

			$filter .= "`$constraint` = :$constraint";
			$parameters[] = [":$constraint", $constraints[$constraint]];
		}

		return self::select('*', $filter, $parameters);
	}

	public static function destroy(int $id): bool
	{
		$model = self::find($id);
		if ($model->exists()) {
			return $model->delete();
		}

		return false;
	}

	/**
	 * Load all Models, optionally with a limit or pagination
	 *
	 * @param int $limitOrPage Treated as page if $limit is provided, limit otherwise
	 * @param int $limit The amount to limit by
	 *
	 * @return array|null The found model data, or null
	 */
	public static function all(int $limitOrPage = -1, int $limit = -1): ?array
	{
		$class = get_called_class();
		$model = new $class;

		$filter = '';
		$parameters = [];

		// If the user wants to paginate
		if ($limitOrPage >= 1 && $limit >= 0) {
			// Pagination calculation
			$page = ($limitOrPage - 1) * $limit;

			$filter = 'LIMIT :page, :limit';
			$parameters[] = [':page', $page, \PDO::PARAM_INT];
			$parameters[] = [':limit', $limit, \PDO::PARAM_INT];
		}
		// If the user wants an offset
		else if ($limitOrPage >= 0) {
			$filter = 'LIMIT :limit';
			$parameters[] = [':limit', $limitOrPage, \PDO::PARAM_INT];
		}

		return $model->selectAll(
			'*', "ORDER BY {$model->getSort()} ASC $filter",
			$parameters
		);
	}


	/**
	 * Retreive Model, or instantiate
	 *
	 * Usage:
	 * $model = \App\Model\Example::firstOrNew(['name' => 'Example name']);
	 *
	 * @param $search Retrieve by
	 * @param $data Instantiate with search plus data
	 *
	 * @return Model The Model
	 */
	public static function firstOrNew(array $search, array $data = []): Model
	{
		$model = self::search($search);

		if (!$model->exists()) {
			$class = get_called_class();
			$model = new $class;
			$model->fill($search);
			$model->fill($data);
		}

		return $model;
	}

	/**
	 * Create new Model
	 *
	 * Usage:
	 * $model = \App\Model\Example::create(['name' => 'Example name']);
	 *
	 * @param $data Create with this data
	 *
	 * @return Model The Model
	 */
	public static function create(array $data): Model
	{
		$class = get_called_class();
		$model = new $class;
		$model->fill($data);
		$model->save();
		return $model;
	}

	/**
	 * Retreive Model, create if it doesn't exist
	 *
	 * Usage:
	 *	$model = \App\Model\AddressModel::firstOrCreate(
	 *		['zip_code' => '1234AB', 'house_number' => 3],
	 *		['street' => 'Example lane']);
	 *
	 * @param $search Retrieve by
	 * @param $data Data used for creation
	 *
	 * @return Model The Model
	 */
	public static function firstOrCreate(array $search, array $data = []): Model
	{
		$model = self::firstOrNew($search, $data);

		if (!$model->exists()) {
			$model->save();
		}

		return $model;
	}

	/**
	 * Update Model, create if it doesn't exist
	 *
	 * Usage:
	 *	$model = \App\Model\FlightModel::updateOrCreate(
	 *		['departure' => 'Oakland', 'desination' => 'San Diego'],
	 *		['price' => 99]);
	 *
	 * @param $search Retrieve by
	 * @param $data Data used for creation
	 *
	 * @return Model The Model
	 */
	public static function updateOrCreate(array $search, array $data): Model
	{
		$model = self::firstOrNew($search, $data);
		$model->fill($data);
		$model->save();

		return $model;
	}

}

// @Todo
// - Generate rules from database table
// - Make count work without 'this' context
