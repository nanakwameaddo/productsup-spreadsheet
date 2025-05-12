<?php

namespace App\Application\Http\Contract\shared;

interface HttpGetClientContract
{
    public function get(string $url, string $username, string $password): string;

}
