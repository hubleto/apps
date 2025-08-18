<?php

namespace HubletoApp\Community\Deals\Extendibles;

class ProductTypes extends \HubletoMain\Extendible
{
  public function getItems(): array
  {
    return [
      10 => 'deal.identifier',
      11 => 'deal.price_excl_vat',
    ];
  }

}