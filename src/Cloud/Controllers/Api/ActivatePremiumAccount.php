<?php

namespace HubletoApp\Community\Cloud\Controllers\Api;

use HubletoApp\Community\Cloud\PremiumAccount;

class ActivatePremiumAccount extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $premiumAccount = $this->main->load(PremiumAccount::class);
    $premiumAccount->activatePremiumAccount();
    $this->main->router->redirectTo('cloud');
  }

}
