<?php

namespace App\Adapters\Console;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'sync:xml-to-sheets',
    description: 'Processes a local or remote xml file  and syncs its data to a Google Sheet'
)]
class ProcessXMLToSheetsCommand extends BaseDataToSheetsCommand
{
    protected static $defaultName = 'sync:file-to-sheets';
    protected string  $fileType = 'XML';

}
