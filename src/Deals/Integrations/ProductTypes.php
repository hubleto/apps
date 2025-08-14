<?php

namespace HubletoApp\Community\Deals\Integrations;

class ProductTypes extends \HubletoMain\Integration
{
  public function getItems(): array
  {
    return [
      10 => 'deal.identifier',
      11 => 'deal.price',
    ];
  }

}