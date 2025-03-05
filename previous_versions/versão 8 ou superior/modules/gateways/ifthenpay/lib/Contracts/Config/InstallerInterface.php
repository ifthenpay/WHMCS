<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Config;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface InstallerInterface
{
    public function install(): void;
    public function uninstall(): void;
}
