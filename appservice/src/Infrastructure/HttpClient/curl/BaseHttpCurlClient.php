<?php

namespace App\Infrastructure\HttpClient\curl;

use App\Domain\Exceptions\NotFoundException;
use App\Domain\Exceptions\ServiceUnavailableException;
use App\Domain\Exceptions\UnauthorizedException;

class BaseHttpCurlClient
{

    protected mixed $data;
    public function common(string $url, string $username, string $password, $headers = [], string $method = "GET" ): string
    {
        $method = strtoupper($method);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,   CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,    CURLOPT_USERPWD,"{$username}:{$password}");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if($method === "POST") {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);

        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch,    CURLOPT_FTP_USE_EPSV,false);
        curl_setopt($ch,    CURLOPT_TIMEOUT, 10);
        curl_setopt($ch,    CURLOPT_FAILONERROR, true);

        $data = curl_exec($ch);

        if ($data === false) {
            $errorCode = curl_errno($ch);
            curl_close($ch);

            match (true) {
                $errorCode === CURLE_COULDNT_CONNECT => throw new ServiceUnavailableException($url),
                $errorCode === 67 => throw new UnauthorizedException("Access denied to: {$url}"),
                default => throw new NotFoundException(),
            };
        }

        curl_close($ch);

        return $data;
    }
}