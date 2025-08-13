<?php

namespace HubletoApp\Community\Products;

class Loader extends \HubletoMain\App
{
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
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mProduct = $this->main->load(\HubletoApp\Community\Products\Models\Product::class);
      $mProductGroup = $this->main->load(\HubletoApp\Community\Products\Models\Group::class);

      $mProductGroup->dropTableIfExists()->install();
      $mProduct->dropTableIfExists()->install();
    }
  }

}
