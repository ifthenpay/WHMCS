<?php

namespace WHMCS\Module\Gateway\ifthenpaylib\Services;


class IfthenpayHttpClient
{
	/**
	 * Make a GET request
	 */
	public static function get($url, $data = [], $headers = [])
	{
		$ch = curl_init();

		if(!empty($data)){
			$url .= '?' . http_build_query($data); 
		}

		curl_setopt_array($ch, [
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER     => self::formatHeaders($headers),
			CURLOPT_TIMEOUT        => 30,
		]);

		$response = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);

		if ($error) {
			throw new \Exception("cURL GET Error: $error");
		}

		return json_decode($response, true);
	}

	/**
	 * Make a POST request
	 */
	public static function post($url, $data = [], $headers = ['Content-Type' => 'application/json'], string $returnDataType = 'json')
	{
		$ch = curl_init();

		if (isset($headers['Content-Type']) && $headers['Content-Type'] == 'application/json') {
			$postFields = json_encode($data);
		} else {
			$postFields = http_build_query($data);
		}

		curl_setopt_array($ch, [
			CURLOPT_URL            => $url,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $postFields,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER     => self::formatHeaders($headers),
			CURLOPT_TIMEOUT        => 30,
		]);

		$response = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);

		if ($error) {
			throw new \Exception("cURL POST Error: $error");
		}

		switch ($returnDataType) {
			case 'json':
				return json_decode($response, true);
			case 'text':
				return $response;
			default:
				return $response;
				break;
		}
	}

	/**
	 * Format headers for cURL
	 */
	private static function formatHeaders($headers): array
	{
		$formatted = [];
		foreach ($headers as $key => $value) {
			$formatted[] = "$key: $value";
		}
		return $formatted;
	}
}
