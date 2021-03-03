<?php

namespace App\Classes;

class Db {

	protected static $db;

	protected static $columns = [];
	protected static $sections = [];
	protected static $pages = [];

	//-------------------------------------//

	public static function load(): void {
		try {
			$host = Config::c('DB_HOST');
			$name = Config::c('DB_NAME');
			self::$db = new \PDO(
				"mysql:host=$host;dbname=$name;charset=utf8mb4",
				Config::c('DB_USERNAME'),
				Config::c('DB_PASSWORD')
			);
		} catch (\PDOException $e) {
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
	}

	//-------------------------------------//

	/**
	 * Get the PDO connection object
	 *
	 * @return PDO
	 */
	public static function get(): \PDO {
		return self::$db;
	}

	/**
	 * Get all columns
	 *
	 * @return array
	 */
	public static function getColumns(): array
	{
		return self::$columns;
	}

	/**
	 * Get all sections
	 *
	 * @return array
	 */
	public static function getSections(): array
	{
		return self::$sections;
	}

	/**
	 * Get all pages
	 *
	 * @return array
	 */
	public static function getPages(): array
	{
		return self::$pages;
	}

	/**
	 * Store columns
	 *
	 * @param array $columns
	 *
	 * @return void
	 */
	public static function setColumns(array $columns): void
	{
		self::$columns = $columns;
	}

	/**
	 * Store sections
	 *
	 * @param array $sections
	 *
	 * @return void
	 */
	public static function setSections(array $sections): void
	{
		self::$sections = $sections;
	}

	/**
	 * Store pages
	 *
	 * @param array $pages
	 *
	 * @return void
	 */
	public static function setPages(array $pages): void
	{
		self::$pages = $pages;
	}

}
