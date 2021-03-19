<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Utility;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Token {

    private $key;
    private $iv;
    private $cipher = 'eKbL_fjE@i#u;;VlX..6s??$$POx1d2!!lsjk__WQAH_seuieks,xnfhdmz,x';

    private function init(): void
    {
        $this->key = hash( 'sha256', $this->cipher, true );
        $this->iv = mcrypt_create_iv(32);
    }

    public function encrypt( string $input ): string {
        $this->init();
        return urlencode( base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, $this->key, $input, MCRYPT_MODE_ECB, $this->iv ) ) );
    }

    public function decrypt( string $input ): string {
        $this->init();
        return mcrypt_decrypt( MCRYPT_RIJNDAEL_256, $this->key, base64_decode( urldecode( $input ) ), MCRYPT_MODE_ECB, $this->iv );
    }

}