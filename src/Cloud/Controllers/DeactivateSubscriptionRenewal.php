<?php

namespace HubletoApp\Community\Cloud\Controllers;

use HubletoApp\Community\Cloud\PremiumAccount;

class DeactivateSubscriptionRenewal extends \HubletoMain\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $premiumAccount = $this->main->di->create(PremiumAccount::class);
    $premiumAccount->deactivateSubscriptionRenewal();
    $this->main->router->redirectTo('cloud');
  }

}
