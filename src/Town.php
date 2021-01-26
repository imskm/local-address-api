<?php

namespace LocalAddressApi;

use \PDO;
use LocalAddressApi\State;
use LocalAddressApi\District;

/**
 * Town class represent a single town of a pincode
 *
 *
 */
class Town
{
	/**
	 * @var PDO 
	 */
	private $pdo;

	public $id;
	public $town;
	public $pincode;
	public $district_id;
	public $state_id;

	private $district;
	private $state;

	public function __construct($id, $town, $pincode, $district_id, $state_id, PDO $pdo)
	{
		$this->id 			= $id;
		$this->town 		= $town;
		$this->pincode 		= $pincode;
		$this->district_id 	= $district_id;
		$this->state_id 	= $state_id;
		$this->pdo 			= $pdo;

		$this->district 	= null;
		$this->state 		= null;
	}

	public function district()
	{
		if (!is_null($this->district)) {
			return $this->district;
		}
		$district_rec = $this->fetchDistrictFromDB($this->district_id);

		return $this->district = new District(
			$district_rec->id,
			$district_rec->district,
			$this->pdo
		);
	}

	private function fetchDistrictFromDB($district_id)
	{
		$table = AddressTablesInfo::DISTRICT_TABLE;
		$sql = "SELECT * FROM {$table} WHERE id = :id";
		$st = $this->pdo->prepare($sql);
		$st->bindValue(":id", $district_id, PDO::PARAM_INT);
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

	public function state()
	{
		if (!is_null($this->state)) {
			return $this->state;
		}
		$state_rec = $this->fetchStateFromDB($this->state_id);

		return $this->state = new State(
			$state_rec->id,
			$state_rec->state,
			$this->pdo
		);
	}

	private function fetchStateFromDB($state_id)
	{
		$table = AddressTablesInfo::STATE_TABLE;
		$sql = "SELECT * FROM {$table} WHERE id = :id";
		$st = $this->pdo->prepare($sql);
		$st->bindValue(":id", $state_id, PDO::PARAM_INT);
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
}
