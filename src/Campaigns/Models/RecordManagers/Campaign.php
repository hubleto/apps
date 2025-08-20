<?php

namespace HubletoApp\Community\Campaigns\Models\RecordManagers;

use HubletoApp\Community\Settings\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

use HubletoApp\Community\Leads\Models\RecordManagers\LeadCampaign;

class Campaign extends \HubletoMain\RecordManager
{
  public $table = 'campaigns';

  /** @return BelongsTo<User, covariant Lead> */
  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

  /** @return hasMany<LeadDocument, covariant Lead> */
  public function LEADS(): HasMany
  {
    return $this->hasMany(LeadCampaign::class, 'id_lead', 'id');
  }

}
