<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Config;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Config\IfthenpayInstall;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;

class IfthenpaySql extends IfthenpayInstall
{
    const MULTIBANCO_TABLE = 'ifthenpay_multibanco';
    const MBWAY_TABLE = 'ifthenpay_mbway';
    const PAYSHOP_TABLE = 'ifthenpay_payshop';
    const CCARD_TABLE = 'ifthenpay_ccard';

    private $statusEnum = ['pending', 'paid'];
    private $schema;

    public function __construct(IfthenpayLogger $ifthenpayLogger)
    {
        parent::__construct($ifthenpayLogger);
        $this->schema = Capsule::schema();
    }


    private function createMultibancoTable(): void
    {
        if (!$this->schema->hasTable(self::MULTIBANCO_TABLE)) {
            $this->schema->create(
                self::MULTIBANCO_TABLE,
                function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->string('entidade', 5);
                    $table->string('referencia', 9);
                    $table->string('order_id', 50);
                    $table->string('requestId', 50)->nullable();
                    $table->string('validade', 15)->nullable();
                    $table->enum('status', $this->statusEnum);
                    $table->index('referencia');
                    $table->timestamps();
                }
            );
            $this->ifthenpayLogger->info($this->paymentMethod . 'database table created with success');
        }
        
    }

    private function createMbwayTable(): void
    {
        if (!$this->schema->hasTable(self::MBWAY_TABLE)) {
            $this->schema->create(
                self::MBWAY_TABLE,
                function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->string('id_transacao', 20);
                    $table->string('telemovel', 20);
                    $table->string('order_id', 50);
                    $table->enum('status', $this->statusEnum);
                    $table->index('id_transacao');
                    $table->timestamps();
                }
            );
            $this->ifthenpayLogger->info($this->paymentMethod . 'database table created with success');
        }
        
    }

    private function createPayshopTable(): void
    {
        if (!$this->schema->hasTable(self::PAYSHOP_TABLE)) {
            $this->schema->create(
                self::PAYSHOP_TABLE,
                function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->string('id_transacao', 20);
                    $table->string('referencia', 13);
                    $table->string('validade', 8);
                    $table->string('order_id', 50);
                    $table->enum('status', $this->statusEnum);
                    $table->index('referencia');
                    $table->timestamps();
                }
            );
            $this->ifthenpayLogger->info($this->paymentMethod . 'database table created with success');
        }
        
    }

    private function createCCardTable(): void
    {
        if (!$this->schema->hasTable(self::CCARD_TABLE)) {
            $this->schema->create(
                self::CCARD_TABLE,
                function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->string('requestId', 50);
                    $table->string('order_id', 50);
                    $table->enum('status', ['paid', 'cancel', 'error', 'pending']);
                    $table->index('requestId');
                    $table->timestamps();
                }
            );
            $this->ifthenpayLogger->info($this->paymentMethod . 'database table created with success');
        }
    }

    private function createIfthenpaySql(): void
    {
            try {
                switch($this->paymentMethod) {
                    case Gateway::MULTIBANCO:
                        $this->createMultibancoTable();
                        break;
                    case Gateway::MBWAY:
                        $this->createMbwayTable();
                        break;
                    case Gateway::PAYSHOP:
                        $this->createPayshopTable();
                        break;
                    case Gateway::CCARD:
                        $this->createCCardTable();
                        break;
                    default:
                        throw new \Exception('Database table not exist');
                }
            } catch (\Throwable $th) {
                throw $th;
            }
    }

    public function removePaymentUrlFromCCardTable(): void
    {
         if ($this->schema->hasTable(self::CCARD_TABLE) && $this->schema->hasColumn(self::CCARD_TABLE, 'paymentUrl')) {
            $this->schema->table(
                self::CCARD_TABLE,
                function ($table) {
                    $table->dropColumn('paymentUrl');
                }
            );
        }
    }

    public function addRequestIdValidadeToMultibancoTable(): void
    {
        if ($this->schema->hasTable(self::MULTIBANCO_TABLE) && !$this->schema->hasColumn(self::MULTIBANCO_TABLE, 'requestId') && !$this->schema->hasColumn(self::MULTIBANCO_TABLE, 'validade')) {
            $this->schema->table(self::MULTIBANCO_TABLE, function($table) {
                $table->string('requestId', 50)->nullable();
                $table->string('validade', 15)->nullable();
                $this->ifthenpayLogger->info('Multibanco table changed with success');
            });
        }
    }

    public function install(): void
    {
        $this->createIfthenpaySql();
    }

    public function uninstall(): void
    {
        //void
    }
}
