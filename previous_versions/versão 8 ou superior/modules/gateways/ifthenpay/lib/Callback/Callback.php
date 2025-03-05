<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;

class Callback
{

    private $activateEndpoint = 'https://ifthenpay.com/api/endpoint/callback/activation';
    private $webService;
    private $urlCallback;
    private $chaveAntiPhishing;
    private $backofficeKey;
    private $entidade;
    private $subEntidade;
    private $ifthenpayLogger;

    private $urlCallbackParameters = [
        Gateway::MULTIBANCO => '?chave=[CHAVE_ANTI_PHISHING]&entidade=[ENTIDADE]&referencia=[REFERENCIA]&valor=[VALOR]',
        Gateway::MBWAY => '?chave=[CHAVE_ANTI_PHISHING]&referencia=[REFERENCIA]&id_pedido=[ID_TRANSACAO]&valor=[VALOR]&estado=[ESTADO]',
        Gateway::PAYSHOP => '?chave=[CHAVE_ANTI_PHISHING]&id_cliente=[ID_CLIENTE]&id_transacao=[ID_TRANSACAO]&referencia=[REFERENCIA]&valor=[VALOR]&estado=[ESTADO]',
        Gateway::CCARD => '?chave=[CHAVE_ANTI_PHISHING]&requestId=[REQUEST_ID]&orderId=[ORDER_ID]&valor=[VALOR]'
    ];

    public function __construct(GatewayDataBuilder $data, WebService $webService, IfthenpayLogger $ifthenpayLogger)
    {
        $this->webService = $webService;
        $this->backofficeKey = $data->getData()->backofficeKey;
        $this->entidade = $data->getData()->entidade;
        $this->subEntidade = $data->getData()->subEntidade;
        $this->ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_CALLBACK)->getLogger();
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
        $request = $this->webService->postRequest(
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
            throw new \Exception(\Lang::trans('errorCallbackActivation'));
        }
    }

    public function make(string $paymentType, string $moduleLink, bool $activateCallback = false): void
    {
        $this->createAntiPhishing();
        $this->createUrlCallback($paymentType, $moduleLink);
        $this->ifthenpayLogger->info('callback data created with sucess', [
                'paymentType' => $paymentType,
                'chaveAntiPhishing' => $this->chaveAntiPhishing,
                'callbackUrl' => $this->urlCallback,
            ]
        );
        if ($activateCallback) {
            $this->activateCallback();
            $this->ifthenpayLogger->info('callback activated with sucess', [
                    'chave' => $this->backofficeKey,
                    'entidade' => $this->entidade,
                    'subentidade' => $this->subEntidade,
                    'apKey' => $this->chaveAntiPhishing,
                    'urlCb' => $this->urlCallback,
                ]
            );
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
