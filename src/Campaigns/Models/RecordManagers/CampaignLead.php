<?php

namespace HubletoApp\Community\Campaigns\Models\RecordManagers;

use HubletoApp\Community\Leads\Models\RecordManagers\Lead;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignLead extends \HubletoMain\RecordManager
{
  public $table = 'campaigns_leads';

  /** @return BelongsTo<Campaign, covariant CampaignProduct> */
  public function CAMPAIGN(): BelongsTo
  {
    return $this->belongsTo(Campaign::class, 'id_campaign', 'id');
  }

  /** @return BelongsTo<Product, covariant OrderProduct> */
  public function LEAD(): BelongsTo
  {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }
}
