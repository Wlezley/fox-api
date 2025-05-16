<?php

declare(strict_types=1);

namespace App\ApiModule\Presenters;

use Nette;
use Nette\Application\Attributes\Requires;

/**
 * Handles 4xx HTTP error responses.
 */
#[Requires(methods: '*', forward: true)]
final class Error4xxPresenter extends Nette\Application\UI\Presenter
{
    public function renderDefault(Nette\Application\BadRequestException $exception): void
    {
        $this->sendJson([
            'status' => 'error',
            'error' => [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ]
        ]);
    }
}
