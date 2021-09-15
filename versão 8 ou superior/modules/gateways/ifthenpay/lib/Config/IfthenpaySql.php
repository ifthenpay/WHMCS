<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Config;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Config\IfthenpayInstall;


class IfthenpaySql extends IfthenpayInstall
{

    private $statusEnum = ['pending', 'paid'];
    private $schema;

    public function __construct(IfthenpayLogger $ifthenpayLogger)
    {
        parent::__construct($ifthenpayLogger);
        $this->schema = Capsule::schema();
    }


    private function createMultibancoTable(): void
    {
        if (!$this->schema->hasTable('ifthenpay_multibanco')) {
            $this->schema->create(
                'ifthenpay_multibanco',
                function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->string('entidade', 5);
                    $table->string('referencia', 9);
                    $table->string('order_id', 50);
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
        if (!$this->schema->hasTable('ifthenpay_mbway')) {
            $this->schema->create(
                'ifthenpay_mbway',
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
        if (!$this->schema->hasTable('ifthenpay_payshop')) {
            $this->schema->create(
                'ifthenpay_payshop',
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
        if (!$this->schema->hasTable('ifthenpay_ccard')) {
            $this->schema->create(
                'ifthenpay_ccard',
                function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->string('requestId', 50);
                    $table->string('order_id', 50);
                    $table->string('paymentUrl', 1000);
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
                    case 'multibanco':
                        $this->createMultibancoTable();
                        break;
                    case 'mbway':
                        $this->createMbwayTable();
                        break;
                    case 'payshop':
                        $this->createPayshopTable();
                        break;
                    case 'ccard':
                        $this->createCCardTable();
                        break;
                    default:
                        throw new \Exception('Database table not exist');
                }
            } catch (\Throwable $th) {
                throw $th;
            }
    }

    public function changeCcardTable(): void
    {
        if ($this->schema->hasTable('ifthenpay_ccard') && Capsule::select(Capsule::raw('SHOW FIELDS FROM ifthenpay_ccard'))[3]->Type === 'varchar(250)') {
            Capsule::statement('ALTER TABLE ifthenpay_ccard  MODIFY paymentUrl varchar(1000)');
            $this->ifthenpayLogger->info('ccard table changed with success');           
        }
    }

    public function install(): void
    {
        $this->createIfthenpaySql();
    }

    public function uninstall(): void
    {
        /*if ($this->userPaymentMethods) {
            $this->deleteIfthenpaySql();
            $this->deleteShopSql();
        }
        $this->deleteIfthenpayLogSql();*/
    }
}
