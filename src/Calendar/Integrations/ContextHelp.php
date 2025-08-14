<?php

namespace HubletoApp\Community\Calendar\Integrations;

class ContextHelp extends \HubletoMain\Integration
{
  public function getItems(): array
  {
    return [
      'calendar' => [
        'en' => 'en/apps/community/calendar',
      ],
    ];
  }

}