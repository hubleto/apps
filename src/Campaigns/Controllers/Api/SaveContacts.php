<?php

namespace HubletoApp\Community\Campaigns\Controllers\Api;

use HubletoApp\Community\Contacts\Models\Contact;
use HubletoApp\Community\Campaigns\Models\CampaignContact;

class SaveContacts extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idCampaign = $this->main->urlParamAsInteger('idCampaign');
    $contactIds = $this->main->urlParamAsArray('contactIds');

    $mContact = $this->main->load(Contact::class);
    $mCampaignContact = $this->main->load(CampaignContact::class);

    $campaignContacts = $mCampaignContact->record->where('id_campaign', $idCampaign)->pluck('id_contact')?->toArray();
    if (!is_array($campaignContacts)) $campaignContacts = [];

    // pridam nove kontakty

    foreach ($contactIds as $idContact) {
      $idContact = (int) $idContact;
      if ($idContact <= 0) continue;
      if (!in_array($idContact, $campaignContacts)) {
        $mCampaignContact->record->recordCreate([
          'id_campaign' => $idCampaign,
          'id_contact' => $idContact,
        ]);
      }
    }

    // zmazem, ktore treba zmazat

    foreach ($campaignContacts as $idContact) {
      if (!in_array($idContact, $contactIds)) {
        $mCampaignContact->record->recordDelete($idContact);
      }
    }

    return $contactIds;
  }
}
