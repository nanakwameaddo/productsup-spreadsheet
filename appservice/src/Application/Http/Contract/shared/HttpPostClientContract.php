<?php

namespace App\Application\Http\Contract\shared;

interface HttpPostClientContract
{
    public function post(string $url, string $username, string $password, array $headers = []): string;

}
