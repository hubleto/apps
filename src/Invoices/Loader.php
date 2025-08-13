<?php

namespace HubletoApp\Community\Invoices;

class Loader extends \HubletoMain\App
{

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
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
      $this->main->load(Models\Invoice::class)->dropTableIfExists()->install();
      $this->main->load(Models\InvoiceItem::class)->dropTableIfExists()->install();
    }
  }

}