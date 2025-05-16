<?php

declare(strict_types=1);

namespace App\ApiModule\Presenters;

use App\Models\ProductManager;
use Nette;
use Nette\Database\Explorer;
use Tracy\Debugger;

final class ProductPresenter extends Nette\Application\UI\Presenter
{
    /** @var Explorer @inject */
    public Explorer $db;

    public function __construct(
        public ProductManager $productManager
    ) {
        Debugger::$showBar = false;
    }

    public function actionDefault(): void
    {
        $this->productManager->processRequest();
        $this->sendJson($this->productManager->getDataForResponse());
    }
}
