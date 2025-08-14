<?php

namespace HubletoApp\Community\Campaigns\Integrations;

class MailTemplateVariables extends \HubletoMain\Integration
{
  public function getItems(): array
  {
    return [
      'campaign.utmSource',
      'campaign.utmCampaign',
      'campaign.utmTerm',
      'campaign.utmContent',
    ];
  }

}