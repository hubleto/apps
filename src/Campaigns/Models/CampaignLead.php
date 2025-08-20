<?php

namespace HubletoApp\Community\Campaigns\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Leads\Models\Lead;

class CampaignLead extends \Hubleto\Framework\Models\Model
{
  public string $table = 'campaigns_leads';
  public string $recordManagerClass = RecordManagers\CampaignLead::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'CAMPAIGN'   => [ self::BELONGS_TO, Campaign::class, 'id_campaign', 'id'],
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_campaign' => (new Lookup($this, $this->translate('Campaign'), Campaign::class))->setRequired(),
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Campaign Leads';
    $description->ui["addButtonText"] = $this->translate("Add lead");

    if ($this->main->urlParamAsInteger('idCampaign') > 0) {
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }
}
