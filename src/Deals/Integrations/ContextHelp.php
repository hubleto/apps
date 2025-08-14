<?php

namespace HubletoApp\Community\Deals\Integrations;

class ContextHelp extends \HubletoMain\Integration
{
  public function getItems(): array
  {
    return [
      'deals' => [
        'en' => 'en/apps/community/deals',
      ],
    ];
  }

}