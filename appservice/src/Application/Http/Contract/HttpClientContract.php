<?php

namespace App\Application\Http\Contract;

interface HttpClientContract
{
    public function exception(mixed $error): string;



}
