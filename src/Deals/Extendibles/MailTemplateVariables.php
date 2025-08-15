<?php

namespace HubletoApp\Community\Deals\Extendibles;

class MailTemplateVariables extends \HubletoMain\Extendible
{
  public function getItems(): array
  {
    return [
      'deal.identifier',
      'deal.price',
    ];
  }

}