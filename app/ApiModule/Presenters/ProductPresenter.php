<?php

declare(strict_types=1);

namespace App\ApiModule\Presenters;

use App\Models\ProductManager;
use Nette;
use Nette\Database\Explorer;
use Tracy\Debugger;

/**
 * Presenter for handling the /v1/product API endpoint.
 *
 * Uses ProductManager to process HTTP requests and returns JSON responses.
 */
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
