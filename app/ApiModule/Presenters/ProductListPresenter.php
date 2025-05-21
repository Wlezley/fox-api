<?php

declare(strict_types=1);

namespace App\ApiModule\Presenters;

use App\Models\ApiManager\ProductListManager;
use Nette;
use Nette\Database\Explorer;
use Tracy\Debugger;

final class ProductListPresenter extends Nette\Application\UI\Presenter
{
    /** @var Explorer @inject */
    public Explorer $db;

    public function __construct(
        public ProductListManager $productListManager
    ) {
        Debugger::$showBar = false;
    }

    /**
     * Handles the default action for the /products endpoint.
     *
     * Processes the request using ProductListManager and sends a JSON response.
     */
    public function actionDefault(): void
    {
        $this->productListManager->processRequest();
        $this->sendJson($this->productListManager->getDataForResponse());
    }
}
