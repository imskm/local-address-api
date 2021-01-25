<?php

namespace LocalAddressApi;

use \PDO;

/**
 * State class represent a state of a town
 *
 *
 */
class State
{
	private $pdo;

	public $id;
	public $state;

	public function __construct($id, $state, PDO $pdo)
	{
		$this->id 			= $id;
		$this->state 	= $state;
		$this->pdo 			= $pdo;
	}
}
