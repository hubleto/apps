<?php

namespace HubletoApp\Community\Deals\Extendibles;

class ContextHelp extends \HubletoMain\Extendible
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