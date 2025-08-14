<?php

namespace HubletoApp\Community\Deals\Integrations;

class MailTemplateVariables extends \HubletoMain\Integration
{
  public function getItems(): array
  {
    return [
      'deal.identifier',
      'deal.price',
    ];
  }

}