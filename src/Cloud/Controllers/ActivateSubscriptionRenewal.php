<?php

namespace HubletoApp\Community\Cloud\Controllers;

use HubletoApp\Community\Cloud\PremiumAccount;

class ActivateSubscriptionRenewal extends \HubletoMain\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $premiumAccount = $this->main->load(PremiumAccount::class);
    $premiumAccount->activateSubscriptionRenewal();

    // $currentCredit = $premiumAccount->recalculateCredit();

    // $mPayment = $this->main->load(\HubletoApp\Community\Cloud\Models\Payment::class);
    // $mPayment->record->recordCreate([
    //   'datetime_charged' => date('Y-m-d H:i:s'),
    //   'full_amount' => -$currentCredit,
    //   'type' => $mPayment::TYPE_SUBSCRIPTION_RENEWAL_ACTIVATED,
    // ]);

    $this->main->router->redirectTo('cloud');
  }

}
