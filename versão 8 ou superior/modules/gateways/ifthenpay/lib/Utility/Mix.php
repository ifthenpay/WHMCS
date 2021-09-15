<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Utility;

use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;

class Mix {
	
    private $ifthenpayLogger;

    public function __construct(IfthenpayLogger $ifthenpayLogger)
	{
        $this->ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_ASSETS)->getLogger();
	}

    public function create(string $path): string
    {
        $manifestPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assetVersionList.json';
        if (!file_exists($manifestPath)) {
            $this->ifthenpayLogger->alert('assetVersionList file not exist', ['manifestPath' => $manifestPath]);
            throw new \Exception('assetVersionList file not exist');
        }
        $manifest = json_decode(file_get_contents($manifestPath), true);

        if (!array_key_exists($path, $manifest)) {
            $this->ifthenpayLogger->alert('Unable to locate Mix file', ['path' => $path, 'manifest' => $manifest]);
            throw new \Exception(
                "Unable to locate Mix file: {$path}. Please check your ".
                'webpack.mix.js output paths and try again.'
            );
        }
        $this->ifthenpayLogger->info('mix asset retrieved with success', ['asset' => $manifest[$path]]);
        return $manifest[$path];
    }
}