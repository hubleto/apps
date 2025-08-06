<?php

namespace HubletoApp\Community\Mail\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends \Hubleto\Framework\RecordManager
{
  public $table = 'mails_accounts';
}
