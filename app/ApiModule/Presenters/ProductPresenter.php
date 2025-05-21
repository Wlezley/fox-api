<?php

declare(strict_types=1);

namespace App\ApiModule\Presenters;

use App\Models\ApiManager\ProductHistoryManager;
use App\Models\ApiManager\ProductHistoryPriceManager;
use App\Models\ApiManager\ProductManager;
use Nette;
use Nette\Database\Explorer;
use Tracy\Debugger;

final class ProductPresenter extends Nette\Application\UI\Presenter
{
    /** @var Explorer @inject */
    public Explorer $db;

    public function __construct(
        public ProductManager $productManager,
        public ProductHistoryManager $productHistoryManager,
        public ProductHistoryPriceManager $productHistoryPriceManager
    ) {
        Debugger::$showBar = false;
    }

    /**
     * Handles the default action for the /product endpoint.
     *
     * Processes the request using ProductManager and sends a JSON response.
     */
    public function actionDefault(): void
    {
        $this->productManager->processRequest();
        $this->sendJson($this->productManager->getDataForResponse());
    }

    /**
     * Handles the history action for the /product/history endpoint.
     *
     * Processes the request using ProductHistoryManager and sends a JSON response.
     */
    public function actionHistory(): void
    {
        $this->productHistoryManager->processRequest();
        $this->sendJson($this->productHistoryManager->getDataForResponse());
    }

    /**
     * Handles the price history action for the /product/history/price endpoint.
     *
     * Processes the request using ProductHistoryPriceManager and sends a JSON response.
     */
    public function actionHistoryPrice(): void
    {
        $this->productHistoryPriceManager->processRequest();
        $this->sendJson($this->productHistoryPriceManager->getDataForResponse());
    }
}
