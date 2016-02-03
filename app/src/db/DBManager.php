<?php

namespace db;

/**
 * Class DBManager
 * @package db
 */
final class DBManager
{
	/** @var \mysqli $_connection */
	private static $_connection = null;

	/**
	 * @param string $sql
	 * @return array
	 */
	public static function query(string $sql) : array
	{
		$result = self::_getConnection()->query($sql);
		return $result instanceof \mysqli_result ? $result->fetch_all() : [];
	}

	/**
	 * @return \mysqli
	 */
	private static function _getConnection() : \mysqli
	{
		if (self::$_connection !== null && !self::$_connection->ping()) {
			if (self::$_connection instanceof \mysqli) {
				self::$_connection->close();
			}
			self::$_connection = null;
		} elseif (self::$_connection === null) {

			if (!class_exists('\mysqli')) {
				die('mysqli is not available.');
			}

			$config = self::_getDBConfig();
			$connection = new \mysqli($config['host'], $config['username'], $config['password'], $config['database']);

			if (mysqli_connect_errno() !== 0) {
				die('database error: ' . mysqli_connect_error());
			}

			self::$_connection = $connection;
		}
		return self::$_connection;
	}

	/**
	 * @return array
	 */
	private static function _getDBConfig() : array
	{
		return json_decode(file_get_contents('/vagrant/app/config/mysql.json'), true);
	}
}