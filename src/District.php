<?php

namespace LocalAddressApi;

use \PDO;

/**
 * District class represent a district of a town
 *
 *
 */
class District
{
	private $pdo;

	public $id;
	public $district;

	private $state;

	public function __construct($id, $district, PDO $pdo)
	{
		$this->id 			= $id;
		$this->district 	= $district;
		$this->pdo 			= $pdo;

		$this->state 		= null;
	}
}
