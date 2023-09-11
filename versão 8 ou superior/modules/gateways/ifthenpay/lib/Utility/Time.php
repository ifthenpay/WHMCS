<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Utility;

if (!defined("WHMCS")) {
	die("This file cannot be accessed directly");
}


class Time
{

	public static function getCurrentDateTimeForLisbon(): \DateTime
	{
		$timezone = new \DateTimeZone('Europe/Lisbon');
		$currentDate = new \DateTime('now', $timezone);

		return $currentDate;
	}


	public static function getCurrentDateTimeStringForLisbon(): string
	{
		$timezone = new \DateTimeZone('Europe/Lisbon');
		$currentDate = new \DateTime('now', $timezone);
		$formatedCurrentDate = $currentDate->format('Y-m-d H:i:s');

		return $formatedCurrentDate;
	}

	public static function createDateTimeForLisbonFrom(string $dateTime, string $format = 'Y-m-d H:i:s'): \DateTime
	{
		try {
			$timezone = new \DateTimeZone('Europe/Lisbon');
			$date = \DateTime::createFromFormat($format, $dateTime, $timezone);
		} catch (\Throwable $th) {
			var_dump($th->getMessage());
		}

		return $date;
	}

}
