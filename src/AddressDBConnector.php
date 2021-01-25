<?php

namespace LocalAddressApi;

/**
* Connector class
* Let's app to connect to DB with appropriate DSN
*/
class AddressDBConnector
{
	/**
	 * @var resource  $conn db Connection
	 */
	protected static $db = null;

	protected function __construct() {}

	public static function getConnection($host, $port, $dbname, $username, $password)
	{
		if (self::$db === null) {
			try {
				self::$db = new \PDO(
					self::getConnectionString($host, $port, $dbname),
					$username,
					$password
				);

				// Throw an Exception when Error occurs
				self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			} catch (\Exception $e) {
				throw new \Exception("ERROR : " . $e->getMessage());
			}
		}

		return self::$db;
	}

	private static function getConnectionString($host, $port, $dbname)
	{
		$format = "mysql:host=%s;port=%s;dbname=%s;charset=utf8";

		return sprintf($format, $host, $port, $dbname);
	}
}
