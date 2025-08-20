<?php

namespace HubletoApp\Community\Leads\Models;

use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Deals\Models\Deal;

class LeadDeal extends \Hubleto\Framework\Models\Model
{
  public string $table = 'leads_deals';
  public string $recordManagerClass = RecordManagers\LeadDeal::class;

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id' ],
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setRequired(),
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Add deal';
    return $description;
  }

}
