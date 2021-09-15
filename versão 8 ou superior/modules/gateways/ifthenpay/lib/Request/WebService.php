<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Request;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class WebService
{
    private $client;
    private $response;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getResponseJson(): array
    {
        return json_decode(json_encode(json_decode((string) $this->response->getBody())), true);
    }

    public function getXmlConvertedResponseToArray(): array
    {
        return json_decode(json_encode(json_decode((string) simplexml_load_string($this->response->getBody()->getContents()))[0]), true);
    }

    public function postRequest(string $url, array $data, bool $jsonContentType = false, array $headers = []): self
    {
        try {
            $this->response = $this->client->post(
                $url,
                $jsonContentType ? [
                    'headers' => $headers,
                    'json' => $data
                ] :
                [
                    'headers' => $headers,
                    'form_params' => $data
                ]
            );
            return $this;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getRequest(string $url, array $data = [], $headers = []): self
    {
        try {
            $this->response = $this->client->get($url, [
                'headers' => $headers,
                'query' => $data
            ]);
            return $this;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
