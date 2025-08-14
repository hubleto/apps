<?php

namespace HubletoApp\Community\Customers\Integrations;

class ContextHelp extends \HubletoMain\Integration
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