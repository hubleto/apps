<?php

namespace HubletoApp\Community\Products;

class Loader extends \HubletoMain\App
{

  public array $productTypes = [];

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
      '/^products\/?$/' => Controllers\Products::class,
      '/^products\/groups\/?$/' => Controllers\Groups::class,
      '/^products\/groups(\/(?<recordId>\d+))?\/?$/' => Controllers\Groups::class,
    ]);

    $appMenu = $this->main->load(\HubletoApp\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'products', $this->translate('Products'), 'fas fa-cart-shopping');
    $appMenu->addItem($this, 'products/groups', $this->translate('Groups'), 'fas fa-burger');

    $this->productTypes = $this->collectExtendibles('ProductTypes');
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->load(Models\Group::class)->dropTableIfExists()->install();
      $this->main->load(Models\Product::class)->dropTableIfExists()->install();
      $this->main->load(Models\ProductSupplier::class)->dropTableIfExists()->install();
    }
  }

}
