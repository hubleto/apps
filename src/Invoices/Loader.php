<?php

namespace HubletoApp\Community\Invoices;

class Loader extends \HubletoMain\App
{

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^invoices\/?$/' => Controllers\Invoices::class,
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Invoice($this->main))->dropTableIfExists()->install();
      (new Models\InvoiceItem($this->main))->dropTableIfExists()->install();
    }
  }

}