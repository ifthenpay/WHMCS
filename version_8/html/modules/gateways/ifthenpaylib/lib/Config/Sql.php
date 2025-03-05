<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Config;


if (!defined("WHMCS")) {
	die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;

class Sql
{

	public static function createMultibancoTable(): void
	{
		$schema = Capsule::schema();
		$schema->create(
			Config::MULTIBANCO_TABLE,
			function ($table) {
				/** @var \Illuminate\Database\Schema\Blueprint $table */
				$table->increments('id');
				$table->string('order_id', 50);
				$table->string('amount', 12);
				$table->string('entity', 5);
				$table->string('reference', 50);
				$table->string('transaction_id', 50)->nullable();
				$table->string('deadline', 15)->nullable();
				$table->string('status', 20);
				$table->index('reference');
				$table->timestamps();
			}
		);

		IfthenpayLog::info(Config::MULTIBANCO, 'Ifthenpay Multibanco payments Table created with success.');
	}



	public static function updateMultibancoTableFromVersion_0_0_0(): void
	{
		try {

			if (!self::hasColumnInTable('entidade', Config::MULTIBANCO_TABLE)) {
				return;
			}

			$pdo = Capsule::connection()->getPdo();
			$pdo->beginTransaction();

			$statement = $pdo->prepare(
				'ALTER TABLE ' . Config::MULTIBANCO_TABLE . ' ' .
					'CHANGE COLUMN entidade entity VARCHAR(5), ' .
					'CHANGE COLUMN referencia reference VARCHAR(50), ' .
					'CHANGE COLUMN requestId transaction_id VARCHAR(50), ' .
					'CHANGE COLUMN validade deadline VARCHAR(15), ' .
					'CHANGE COLUMN status status VARCHAR(20), ' .
					'ADD COLUMN amount VARCHAR(12)'
			);

			$statement->execute();
			if ($pdo->inTransaction()) {
				$pdo->commit();
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::MULTIBANCO, 'Error updating database in updateMultibancoTableFromVersion_0_0_0()', $th->__toString());

			if ($pdo->inTransaction()) {
				$pdo->rollBack();
			}

			throw new \Exception("Error updating database");
		}
	}



	public static function createPayshopTable(): void
	{
		$schema = Capsule::schema();
		$schema->create(
			Config::PAYSHOP_TABLE,
			function ($table) {
				/** @var \Illuminate\Database\Schema\Blueprint $table */
				$table->increments('id');
				$table->string('order_id', 50);
				$table->string('amount', 12);
				$table->string('reference', 50);
				$table->string('transaction_id', 50)->nullable();
				$table->string('deadline', 15)->nullable();
				$table->string('status', 20);
				$table->index('reference');
				$table->timestamps();
			}
		);

		IfthenpayLog::info(Config::PAYSHOP, 'Ifthenpay Payshop payments Table created with success.');
	}



	public static function updatePayshopTableFromVersion_0_0_0(): void
	{
		try {

			if (!self::hasColumnInTable('referencia', Config::PAYSHOP_TABLE)) {
				return;
			}

			$pdo = Capsule::connection()->getPdo();
			$pdo->beginTransaction();

			$statement = $pdo->prepare(
				'ALTER TABLE ' . Config::PAYSHOP_TABLE . ' ' .
					'CHANGE COLUMN referencia reference VARCHAR(50), ' .
					'CHANGE COLUMN id_transacao transaction_id VARCHAR(50) NULL, ' .
					'CHANGE COLUMN validade deadline VARCHAR(15) NULL, ' .
					'CHANGE COLUMN status status VARCHAR(20), ' .
					'ADD COLUMN amount VARCHAR(12)'
			);

			$statement->execute();
			if ($pdo->inTransaction()) {
				$pdo->commit();
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::PAYSHOP, 'Error updating database in updatePayshopTableFromVersion_0_0_0()', $th->__toString());

			if ($pdo->inTransaction()) {
				$pdo->rollBack();
			}

			throw new \Exception("Error updating database");
		}
	}



	public static function createMbwayTable(): void
	{
		$schema = Capsule::schema();
		$schema->create(
			Config::MBWAY_TABLE,
			function ($table) {
				/** @var \Illuminate\Database\Schema\Blueprint $table */
				$table->increments('id');
				$table->string('order_id', 50);
				$table->string('amount', 12);
				$table->string('mobile_number', 20)->nullable();
				$table->string('transaction_id', 50)->nullable();
				$table->string('status', 20);
				$table->index('transaction_id');
				$table->timestamps();
			}
		);

		IfthenpayLog::info(Config::MBWAY, 'Ifthenpay Mbway payments Table created with success.');
	}



	public static function updateMbwayTableFromVersion_0_0_0(): void
	{
		try {
			if (!self::hasColumnInTable('telemovel', Config::MBWAY_TABLE)) {
				return;
			}

			$pdo = Capsule::connection()->getPdo();
			$pdo->beginTransaction();

			$statement = $pdo->prepare(
				'ALTER TABLE ' . Config::MBWAY_TABLE . ' ' .
					'CHANGE COLUMN id_transacao transaction_id VARCHAR(50) NULL, ' .
					'CHANGE COLUMN telemovel mobile_number VARCHAR(20) NULL, ' .
					'CHANGE COLUMN status status VARCHAR(20), ' .
					'ADD COLUMN amount VARCHAR(12)'
			);

			$statement->execute();
			if ($pdo->inTransaction()) {
				$pdo->commit();
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::MBWAY, 'Error updating database in updateMbwayTableFromVersion_0_0_0()', $th->__toString());

			if ($pdo->inTransaction()) {
				$pdo->rollBack();
			}

			throw new \Exception("Error updating database");
		}
	}



	public static function hasTable(string $tableName): bool
	{
		$schema = Capsule::schema();
		if (!$schema->hasTable($tableName)) {
			return false;
		}
		return true;
	}


	public static function hasColumnInTable(string $column, string $tableName): bool
	{
		$schema = Capsule::schema();
		if (!$schema->hasTable($tableName)) {
			return false;
		}
		return $schema->hasColumn($tableName, $column);
	}


	public static function createCcardTable(): void
	{
		$schema = Capsule::schema();
		$schema->create(
			Config::CCARD_TABLE,
			function ($table) {
				/** @var \Illuminate\Database\Schema\Blueprint $table */
				$table->increments('id');
				$table->string('order_id', 50);
				$table->string('amount', 12);
				$table->string('transaction_id', 50)->nullable();
				$table->string('status', 20);
				$table->index('transaction_id');
				$table->timestamps();
			}
		);

		IfthenpayLog::info(Config::CCARD, 'Ifthenpay Ccard payments Table created with success.');
	}



	public static function updateCcardTableFromVersion_0_0_0(): void
	{
		try {
			if (!self::hasColumnInTable('requestId', Config::CCARD_TABLE)) {
				return;
			}

			$pdo = Capsule::connection()->getPdo();
			$pdo->beginTransaction();

			$statement = $pdo->prepare(
				'ALTER TABLE ' . Config::CCARD_TABLE . ' ' .
					'CHANGE COLUMN requestId transaction_id VARCHAR(50) NULL, ' .
					'CHANGE COLUMN status status VARCHAR(20), ' .
					'ADD COLUMN amount VARCHAR(12)'
			);
			IfthenpayLog::error(Config::CCARD, 'ran query', Config::CCARD_TABLE);


			$statement->execute();
			if ($pdo->inTransaction()) {
				$pdo->commit();
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::CCARD, 'Error updating database in updateCcardTableFromVersion_0_0_0()', $th->__toString());

			if ($pdo->inTransaction()) {
				$pdo->rollBack();
			}

			throw new \Exception("Error updating database");
		}
	}



	public static function createCofidisTable(): void
	{
		$schema = Capsule::schema();
		$schema->create(
			Config::COFIDIS_TABLE,
			function ($table) {
				/** @var \Illuminate\Database\Schema\Blueprint $table */
				$table->increments('id');
				$table->string('order_id', 50);
				$table->string('amount', 12);
				$table->string('transaction_id', 50)->nullable();
				$table->string('status', 20);
				$table->index('transaction_id');
				$table->timestamps();
			}
		);

		IfthenpayLog::info(Config::COFIDIS, 'Ifthenpay Cofidis payments Table created with success.');
	}



	public static function createPixTable(): void
	{
		$schema = Capsule::schema();
		$schema->create(
			Config::PIX_TABLE,
			function ($table) {
				/** @var \Illuminate\Database\Schema\Blueprint $table */
				$table->increments('id');
				$table->string('order_id', 50);
				$table->string('amount', 12);
				$table->string('transaction_id', 50)->nullable();
				$table->string('payment_url', 255)->nullable();
				$table->string('deadline', 15)->nullable();
				$table->string('status', 20);
				$table->index('transaction_id');
				$table->timestamps();
			}
		);

		IfthenpayLog::info(Config::PIX, 'Ifthenpay Pix payments Table created with success.');
	}



	public static function createIfthenpaygatewayTable(): void
	{
		$schema = Capsule::schema();
		$schema->create(
			Config::IFTHENPAYGATEWAY_TABLE,
			function ($table) {
				/** @var \Illuminate\Database\Schema\Blueprint $table */
				$table->increments('id');
				$table->string('order_id', 50);
				$table->string('amount', 12);
				$table->string('transaction_id', 50)->nullable();
				$table->string('payment_url', 255)->nullable();
				$table->string('deadline', 15)->nullable();
				$table->string('status', 20);
				$table->index('transaction_id');
				$table->timestamps();
			}
		);

		IfthenpayLog::info(Config::IFTHENPAYGATEWAY, 'Ifthenpay Ifthenpaygateway payments Table created with success.');
	}

	// PM_BOILERPLATE

}
