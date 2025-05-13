<?php

namespace App\Adapters\Console;

use App\Application\Config\Contract\FileSourceConfigContract;
use App\Application\File\Contract\FileSaverContract;
use App\Application\File\Factory\FileFetcherFactory;
use App\Application\GoogleDocument\Sheet\Service\GoogleSpreadSheetService;
use App\Application\Logger\Contract\LoggerContract;
use App\Application\Parser\Contract\XmlParserContract;
use App\Application\Product\Factory\CoffeeFeed\ProductCoffeeFeedFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'sync:file-to-sheets',
    description: 'Processes a local or remote file  and syncs its data to a Google Sheet'
)]
class BaseDataToSheetsCommand extends Command
{
    protected static $defaultName = 'sync:file-to-sheets';
    protected string  $fileType = 'n/a';
    public function __construct(
        private readonly FileFetcherFactory                              $fileFetcherFactory,
        private readonly FileSourceConfigContract                        $fileSourceConfig,
        private readonly XmlParserContract                               $simpleXmlParser,
        private readonly ProductCoffeeFeedFactory                        $productFactory,
        private readonly GoogleSpreadSheetService                        $googleSpreadSheetService,
        private readonly LoggerContract                                  $logger,
        private readonly FileSaverContract                               $fileSaver
    ) {
        parent::__construct();

    }

    protected function configure(): void
    {
        $this->addArgument('fileSource', InputArgument::OPTIONAL, 'The username');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $fileSource = $input->getArgument('fileSource') ?? $this->fileSourceConfig->getFileSource();

        try {
            $io->info("Starting file fetch from source: {$fileSource}");
            $fetcher = $this->fileFetcherFactory->create($fileSource);
            $dataContent = $fetcher->fetch($this->fileSourceConfig);
            if ($fileSource == 'remote') {
                $io->info("Saving {$this->fileType} content into local storage..");
                $this->fileSaver->save($dataContent);
            }

            $io->success('File fetched and validated successfully.');

            $io->info("Parsing {$this->fileType} content...");
            $simpleXmlParserContent = $this->simpleXmlParser->parse($dataContent);

            $io->info('Transforming data into sheet-compatible structure...');
            $productFactory = $this->productFactory->create($this->fileSourceConfig->getClientName());
            $data = $productFactory->createFromArray($simpleXmlParserContent);
            $sheetData = $productFactory->productMap($data);

            $io->info('Pushing data to Google Sheets...');
            $sheetID = $this->googleSpreadSheetService->processDataAndWriteToSheet($sheetData);
            $io->success('Data successfully pushed to Google Sheets.');
            $io->info("View the sheet here: https://docs.google.com/spreadsheets/d/{$sheetID}");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->logger->Error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
