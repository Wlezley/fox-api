<?php

declare(strict_types=1);

namespace App\ApiModule\Presenters;

use App\Models\ProductHistoryManager;
use Nette;
use Nette\Database\Explorer;
use Tracy\Debugger;

final class ProductHistoryPresenter extends Nette\Application\UI\Presenter
{
    /** @var Explorer @inject */
    public Explorer $db;

    public function __construct(
        public ProductHistoryManager $productHistoryManager
    ) {
        Debugger::$showBar = false;
    }

    public function actionDefault(): void
    {
        $this->productHistoryManager->processRequest();
        $this->sendJson($this->productHistoryManager->getDataForResponse());
    }
}
