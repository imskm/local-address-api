<?php

namespace LocalAddressApi;

use \PDO;
use LocalAddressApi\AddressTablesInfo;
use LocalAddressApi\AddressDBConnector;

class AddressApi
{
	/**
	 * @var \PDO  Dabase connection object
	 */
	private $pdo;

	/**
	 * @var array  Array of LocalAddressApi\Town 
	 */
	private $towns = [];

	/**
	 * @var string
	 */
	private $pincode;

	public function __construct($host, $port, $dbname, $username, $password, $pincode = null)
	{
		$this->pincode = $pincode;
		$this->pdo = AddressDBConnector::getConnection($host, $port, $dbname, $username, $password);
		if ($this->pincode) {
			$this->validePincodeOrThrow($this->pincode);
		}
	}

	public function towns()
	{
		if ($this->towns) {
			return $this->towns;
		}
		// Validating pincode after towns cache checking avoids pincode validation
		// cpu cost.
		$this->validePincodeOrThrow($this->pincode);

		$towns = $this->fetchTownsFromDB($this->pincode);
		foreach ($towns as $town_rec) {
			$town = new Town(
				$town_rec->id,
				$town_rec->town,
				$town_rec->pincode,
				$town_rec->district_id,
				$town_rec->state_id,
				$this->pdo
			);
			$this->towns[] = $town;
		}

		return $this->towns;
	}

	private function fetchTownsFromDB($pincode)
	{
		$town_table = AddressTablesInfo::TOWN_TABLE;
		$sql = "SELECT * FROM {$town_table} WHERE pincode = :pincode";
		$st = $this->pdo->prepare($sql);
		$st->bindValue(":pincode", $pincode, PDO::PARAM_STR);
		if ($st->execute() === false) {
			$errors = $this->pdo->errorInfo();
			$msg = "";
			if (isset($errors[2])) {
				$msg = $errors[2];
			}
			throw new AddressDBQueryFailedException($msg);
		}

		return $st->fetchAll(PDO::FETCH_OBJ);
	}

	public function town($town_id)
	{
		$town = null;
		$town_rec = $this->fetchTownFromDB($town_id);
		if ($town_rec) {
			$town = new Town(
				$town_rec->id,
				$town_rec->town,
				$town_rec->pincode,
				$town_rec->district_id,
				$town_rec->state_id,
				$this->pdo
			);
		}

		return $town;
	}

	private function fetchTownFromDB($town_id)
	{
		$town_table = AddressTablesInfo::TOWN_TABLE;
		$sql = "SELECT * FROM {$town_table} WHERE id = :id";
		$st = $this->pdo->prepare($sql);
		$st->bindValue(":id", $town_id, PDO::PARAM_INT);
		if ($st->execute() === false) {
			$errors = $this->pdo->errorInfo();
			$msg = "";
			if (isset($errors[2])) {
				$msg = $errors[2];
			}
			throw new AddressDBQueryFailedException($msg);
		}

		return $st->fetch(PDO::FETCH_OBJ);
	}

	private function validePincodeOrThrow($pincode)
	{
		$regex = "/^[1-9][0-9]{5,5}$/";
		if (!$pincode || ($ret = preg_match($regex, $pincode)) === 0 || $ret === false) {
			throw new InvalidPincodeException("Invlaid pincode '{$pincode}' given");
		}
	}
}
