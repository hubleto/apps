<?php

namespace HubletoApp\Community\Leads\Integrations;

class ContextHelp extends \HubletoMain\Integration
{
  public function getItems(): array
  {
    return [
      'leads' => [
        'en' => 'en/apps/community/leads',
      ],
    ];
  }

}