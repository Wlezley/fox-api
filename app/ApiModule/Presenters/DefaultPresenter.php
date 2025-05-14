<?php

declare(strict_types=1);

namespace App\ApiModule\Presenters;

use Nette;
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
        $data['default'] = [
            'status' => [
                'code' => 200,
                'message' => 'OK'
            ]
        ];

        $this->sendJson($data);
    }
}
