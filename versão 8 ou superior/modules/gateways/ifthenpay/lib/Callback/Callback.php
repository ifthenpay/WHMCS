<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Request\RequestFactory;
use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;

class Callback
{

    private $activateEndpoint = 'https://ifthenpay.com/api/endpoint/callback/activation';
    private $webservice;
    private $urlCallback;
    private $chaveAntiPhishing;
    private $backofficeKey;
    private $entidade;
    private $subEntidade;

    private $urlCallbackParameters = [
        'multibanco' => '?payment=multibanco&chave=[CHAVE_ANTI_PHISHING]&entidade=[ENTIDADE]&referencia=[REFERENCIA]&valor=[VALOR]',
        'mbway' => '?payment=mbway&chave=[CHAVE_ANTI_PHISHING]&referencia=[REFERENCIA]&id_pedido=[ID_TRANSACAO]&valor=[VALOR]&estado=[ESTADO]',
        'payshop' => '?payment=payshop&chave=[CHAVE_ANTI_PHISHING]&id_cliente=[ID_CLIENTE]&id_transacao=[ID_TRANSACAO]&referencia=[REFERENCIA]&valor=[VALOR]&estado=[ESTADO]',
    ];

    public function __construct(GatewayDataBuilder $data, WebService $webservice)
    {
        $this->webservice = $webservice;
        $this->backofficeKey = $data->getData()->backofficeKey;
        $this->entidade = $data->getData()->entidade;
        $this->subEntidade = $data->getData()->subEntidade;
    }

    private function createAntiPhishing(): void
    {
        $this->chaveAntiPhishing = md5((string) rand());
    }

    private function createUrlCallback(string $paymentType, string $moduleLink): void
    {
        $this->urlCallback = $moduleLink . $this->urlCallbackParameters[$paymentType];
    }

    private function activateCallback(): void
    {
        $request = $this->webservice->postRequest(
            $this->activateEndpoint,
            [
            'chave' => $this->backofficeKey,
            'entidade' => $this->entidade,
            'subentidade' => $this->subEntidade,
            'apKey' => $this->chaveAntiPhishing,
            'urlCb' => $this->urlCallback,
            ],
            true
        );

        $response = $request->getResponse();
        if (!$response->getStatusCode() === 200 && !$response->getReasonPhrase()) {
            throw new \Exception("Error Activating Callback");
        }
    }

    public function make(string $paymentType, string $moduleLink, bool $activateCallback = false): void
    {
        $this->createAntiPhishing();
        $this->createUrlCallback($paymentType, $moduleLink);
        if ($activateCallback) {
            $this->activateCallback();
        }
    }

    /**
     * Get the value of urlCallback
     */
    public function getUrlCallback(): string
    {
        return $this->urlCallback;
    }

    /**
     * Get the value of chaveAntiPhishing
     */
    public function getChaveAntiPhishing(): string
    {
        return $this->chaveAntiPhishing;
    }
}
