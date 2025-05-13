<?php

namespace App\Application\File\Factory;

use App\Application\Config\Contract\ExternalFeedSourceConfigContract;
use App\Application\File\Contract\FileFetcherContract;
use App\Application\Http\Contract\shared\HttpGetClientContract;
use App\Domain\Exceptions\UnsupportedFileSourceException;
use App\Infrastructure\File\Fetch\LocalXmlFileFetcher;
use App\Infrastructure\File\Fetch\RemoteXmlFetcher;

class FileFetcherFactory
{
    public function __construct(
        private ExternalFeedSourceConfigContract $feedSourceYmlConfig,
        private HttpGetClientContract $httpGetClient
    ) {

    }

    public function create(string $FileSource): FileFetcherContract
    {

        return match (strtolower($FileSource)) {
            'local' => new LocalXmlFileFetcher(),
            'remote' => new RemoteXmlFetcher($this->feedSourceYmlConfig, $this->httpGetClient),
            default => throw new UnsupportedFileSourceException($FileSource),
        };
    }
}
