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

    public function actionOpenapiYaml(): void
    {
        $file = __DIR__ . '/../../../openapi/openapi.yml';
        $this->getHttpResponse()->setContentType('text/yaml');
        $this->sendResponse(new \Nette\Application\Responses\TextResponse(file_get_contents($file)));
    }

    public function actionOpenapiJson(): void
    {
        $file = __DIR__ . '/../../../openapi/openapi.yml';
        $yaml = file_get_contents($file);
        $array = \Symfony\Component\Yaml\Yaml::parse($yaml);
        $json = json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $this->getHttpResponse()->setContentType('application/json');
        $this->sendResponse(new \Nette\Application\Responses\TextResponse($json));
    }
}
