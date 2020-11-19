<?php
/**
 * @category Neo
 * @package ...
 * @copyright Vendo Services, GmbH
 */

class Address {
	private string $city;
	private string $country;
	private int $postalCode;
	private string $streetAddress;

	public function __construct(string $city, string $country, int $postalCode, string $streetAddress) {
		$this->city = $city;
		$this->country = $country;
		$this->postalCode = $postalCode;
		$this->streetAddress = $streetAddress;
	}

	public function __serialize(): array {
		return [
			'city' => $this->city,
			'country' => $this->country,
			'postalCode' => $this->postalCode,
			'streetAddress' => $this->streetAddress
		];
	}

	public function __unserialize(array $input): {
		$this->city = $input['city'] ?? null;
		$this->country = $input["country"] ?? null;
		$this->postalCode = $input["postalCode"] ?? null;
		$this->streetAddress = $input["streetAddress"] ?? null;
	}
}

$mailman->send((Address){
	city: "Oslo",
	postalCode: 10495,
});

$mailman->send((new Address)->__unserialize([
	"city" => "Oslo",
	"postalCode" => 10495,
]);
