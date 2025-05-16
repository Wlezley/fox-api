<?php

declare(strict_types=1);

namespace App\ApiModule\Presenters;

use Nette;
use Nette\Http\Response;
use Tracy\Debugger;

final class DefaultPresenter extends Nette\Application\UI\Presenter
{
    public function __construct()
    {
        Debugger::$showBar = false;
    }

    public function actionDefault(): void
    {
        // Test
        $payload = [
            'status' => 'ok',
            'response' => [
                'code' => Response::S200_OK,
                'message' => 'Test api response'
            ]
        ];

        $this->sendJson($payload);
    }
}
