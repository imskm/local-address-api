<?php

declare(strict_types=1);

use LocalAddressApi\Town;
use LocalAddressApi\District;
use LocalAddressApi\AddressApi;
use PHPUnit\Framework\TestCase;
use LocalAddressApi\InvalidPincodeException;

/**
 * @NOTE This test requires few environment variables:
 *  address_db_username, address_db_password
 */
class AddressApiTest extends TestCase
{
	private $host = "127.0.0.1";
	private $port = "3306";
	private $db_name = "aaivan";

	public function test_address_api_object_can_be_created()
	{
		$username = getenv("address_db_username");
		$password = getenv("address_db_password");
		$pincode  = "743126";
		$this->assertInstanceOf(
			AddressApi::class,
			new AddressApi(
				$this->host, $this->port, $this->db_name, $username, $password, $pincode
			)
		);

	}

	public function test_constructor_takes_arguments()
	{
		$username = getenv("address_db_username");
		$password = getenv("address_db_password");
		$pincode  = "743126";
		$this->assertInstanceOf(
			AddressApi::class,
			new AddressApi(
				$this->host, $this->port, $this->db_name, $username, $password, $pincode
			)
		);
	}

	public function test_address_can_be_loaded_using_pincode()
	{
		$username = getenv("address_db_username");
		$password = getenv("address_db_password");
		$pincode  = "743126";
		$address = new AddressApi($this->host, $this->port, $this->db_name, $username, $password, $pincode);

		$this->assertContainsOnlyInstancesOf(Town::class, $address->towns());
	}

	public function test_address_loading_fails_when_invalid_pincode_given()
	{
		$username = getenv("address_db_username");
		$password = getenv("address_db_password");
		$pincode = "043126";

		$this->expectException(InvalidPincodeException::class);
		$address = new AddressApi($this->host, $this->port, $this->db_name, $username, $password, $pincode);
	}

	public function test_success_returns_correct_number_of_towns_for_specific_pincode()
	{
		$username = getenv("address_db_username");
		$password = getenv("address_db_password");
		$pincode = "743126";

		$address = new AddressApi($this->host, $this->port, $this->db_name, $username, $password, $pincode);
		
		$this->assertContainsOnlyInstancesOf(Town::class, $towns = $address->towns());

		$this->assertEquals(5, count($towns));
		foreach ($towns as $town) {
			$this->assertNotEmpty($town->pincode);
		}
	}

	public function test_success_can_fetch_district_using_town()
	{
		$username = getenv("address_db_username");
		$password = getenv("address_db_password");
		$pincode = "743126";

		$address = new AddressApi($this->host, $this->port, $this->db_name, $username, $password, $pincode);
		
		$this->assertContainsOnlyInstancesOf(Town::class, $towns = $address->towns());

		$this->assertEquals(5, count($towns));
		$town = $towns[0];
		$this->assertInstanceOf(District::class, $district = $town->district());
		$this->assertNotEmpty($district->district);
	}

	public function test_success_single_town_can_be_fetched_using_town_id()
	{
		$username = getenv("address_db_username");
		$password = getenv("address_db_password");
		$pincode  = "743126";
		$town_id  = 1;

		$address = new AddressApi($this->host, $this->port, $this->db_name, $username, $password);
		$this->assertInstanceOf(Town::class, $town = $address->town($town_id));
		$this->assertNotEmpty($town->town);
		$this->assertEquals($town_id, (int) $town->id);
	}

	public function test_success_single_town_object_can_fetch_district_and_state()
	{
		$username = getenv("address_db_username");
		$password = getenv("address_db_password");
		$pincode  = "743126";
		$town_id  = 1;

		$address = new AddressApi($this->host, $this->port, $this->db_name, $username, $password);
		$this->assertInstanceOf(Town::class, $town = $address->town($town_id));
	}

	public function test_success_null_returns_when_invalid_town_id_given()
	{
		$username = getenv("address_db_username");
		$password = getenv("address_db_password");
		$pincode  = "743126";
		$town_id  = "$1l";

		$address = new AddressApi($this->host, $this->port, $this->db_name, $username, $password);
		$this->assertNull($town = $address->town($town_id));
	}

}
