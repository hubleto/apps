<?php

namespace HubletoApp\Community\Customers\Extendibles;

class ContextHelp extends \HubletoMain\Extendible
{
  public function getItems(): array
  {
    return [
      'customers' => [
        'en' => 'en/apps/community/customers',
      ],
    ];
  }

}