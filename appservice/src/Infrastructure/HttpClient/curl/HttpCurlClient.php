<?php

namespace App\Infrastructure\HttpClient\curl;

use App\Application\Http\Contract\shared\HttpGetClientContract;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(HttpGetClientContract::class)]
class HttpCurlClient extends BaseHttpCurlClient implements HttpGetClientContract
{
    public function get(string $url, string $username, string $password): string
    {
        return $this->common($url, $username, $password);
    }

}
