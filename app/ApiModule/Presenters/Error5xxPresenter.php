<?php

declare(strict_types=1);

namespace App\ApiModule\Presenters;

use Nette;
use Nette\Application\Attributes\Requires;
use Nette\Application\Responses\JsonResponse;
use Tracy\ILogger;

/**
 * Handles uncaught exceptions and errors, and logs them.
 */
#[Requires(forward: true)]
final class Error5xxPresenter implements Nette\Application\IPresenter
{
    public function __construct(
        private ILogger $logger,
    ) {}

    public function run(Nette\Application\Request $request): Nette\Application\Response
    {
        /** @var \Exception $exception */
        $exception = $request->getParameter('exception');
        $this->logger->log($exception, ILogger::EXCEPTION);

        $payload = [
            'status' => 'error',
            'error' => [
                'code' => $exception->getCode() ?: 500,
                'message' => $exception->getMessage()
            ]
        ];

        return new JsonResponse($payload);
    }
}
