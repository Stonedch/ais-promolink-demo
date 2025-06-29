<?php

namespace App\Plugins\Esia\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class EsiaService
{
    protected Client $client;

    protected string $code;
    protected string $token;

    protected string $scope;
    protected string $timestamp;
    protected string $clientid;
    protected string $state;
    protected string $clientSecret;
    protected string $redirect;

    protected string $oid;
    protected string $accessToken;
    protected string $refreshToken;

    public function __construct(
        string $code,
        string $token,
    ) {
        $this->code = $code;
        $this->token = $token;

        $this->state = GuidTokenizer::tokenize();
        $this->refreshToken = $this->state;
        $this->timestamp = date("Y.m.d H:i:s +0300");
        $this->scope = config('plugins.Esia.scope');
        $this->clientid = config('plugins.Esia.clientid');
        $this->clientSecret = CryptoPro::getClientSecret("{$this->scope}{$this->timestamp}{$this->clientid}{$this->state}");
        $this->redirect = static::redirect($this->token);

        $this->initHttpClient();
        $this->auth();
    }

    public static function redirect(?string $token = null)
    {
        if (empty($token)) $token = GuidTokenizer::tokenize();
        $redirect = config('plugins.Esia.redirect');
        return "{$redirect}?token={$token}";
    }

    public function oid(): string
    {
        return $this->oid;
    }

    public function contacts(): array
    {
        $contactsElements = [];
        $contacts = static::bearerGet("https://esia.gosuslugi.ru/rs/prns/{$this->oid}/ctts?embed=(elements)", $this->accessToken);
        foreach ($contacts['elements'] as $element) $contactsElements[$element['type']] = @$element['value'] ?: null;
        $this->auth(true);
        return $contactsElements;
    }

    public function address(): array
    {
        $address = $this->bearerGet("https://esia.gosuslugi.ru/rs/prns/{$this->oid}/addrs?embed=(elements)", $this->accessToken);
        if (is_array($address) == false || empty($address)) return [];
        if (is_array($address['elements']) == false || empty($address['elements'])) return [];
        $this->auth(true);
        return $address['elements'];
    }

    public function person(): array
    {
        $person = $this->bearerGet("https://esia.gosuslugi.ru/rs/prns/{$this->oid}?embed=(elements.person)", $this->accessToken);
        $this->auth(true);
        return $person;
    }

    protected function auth(bool $refresh = false): void
    {
        $params = [
            'client_id' => $this->clientid,
            'code' => $this->code,
            'grant_type' => $refresh ? 'refresh_token' : 'authorization_code',
            'client_secret' => $this->clientSecret,
            'state' => $this->state,
            'redirect_uri' => $this->redirect,
            'scope' => $this->scope,
            'timestamp' => $this->timestamp,
            'token_type' => 'Bearer',
            'refresh_token' => $this->refreshToken,
        ];

        $response = $this->client->send(new Request(
            'POST',
            'https://esia.gosuslugi.ru/aas/oauth2/te',
            ['content-type' => 'application/x-www-form-urlencoded'],
            http_build_query($params)
        ));

        $auth = json_decode($response->getBody()->getContents(), true);

        $this->accessToken = $auth['access_token'];
        $this->refreshToken = $auth['refresh_token'];

        if ($refresh == false) {
            $chunks = explode('.', $auth['id_token']);
            $payload = json_decode(static::base64UrlSafeDecode($chunks[1]), true);
            $this->oid = $payload['urn:esia:sbj']['urn:esia:sbj:oid'];
        }
    }

    protected function initHttpClient(bool $verify = false, int $timeout = 10): void
    {
        $this->client = new Client(['verify' => $verify, 'timeout' => $timeout]);
    }

    protected function bearerGet(string $url, string $accessToken): array
    {
        $response = $this->client->send(new Request('GET', $url, ['Authorization' => "Bearer $accessToken"]));
        return json_decode($response->getBody()->getContents(), true);
    }

    protected static function base64UrlSafeDecode(string $string): string
    {
        return base64_decode(strtr($string, '-_', '+/'));
    }
}
