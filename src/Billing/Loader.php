<?php

namespace HubletoApp\Community\Billing;

use HubletoApp\Community\Settings\Models\Permission;

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
      '/^billing\/?$/' => Controllers\BillingAccounts::class,
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->load(Models\BillingAccount::class)->dropTableIfExists()->install();
    }
  }

}
