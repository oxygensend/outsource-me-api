<?php

namespace App\MessageHandler;

use App\Message\DownloadPostalCodes;
use App\Parser\PostalCodesParser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

final class DownloadPostalCodesHandler implements MessageHandlerInterface
{
    public function __construct(readonly private PostalCodesParser $parser,
                                readonly private LoggerInterface   $logger,
                                readonly private Stopwatch         $stopwatch)
    {
    }

    public function __invoke(DownloadPostalCodes $message)
    {
        $this->logger->info('Started dispatching message: DownloadPostalCodes');

        $this->stopwatch->start('time');
        $this->parser->parse();
        $time = $this->stopwatch->stop('time');

        $this->logger->info('Ended dispatching message: DownloadPostalCodes time:' . $time->getDuration() . ' ms');
        $this->logger->info('Successfully  updated postal codes database');

    }
}
