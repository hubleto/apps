<?php

namespace HubletoApp\Community\Mail\Controllers\Api;

class MarkAsRead extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idMail = $this->main->urlParamAsInteger('idMail');
    $mMail = $this->main->load(\HubletoApp\Community\Mail\Models\Mail::class);
    $mMail->record->find($idMail)->update(['datetime_read' => date('Y-m-d H:i:s')]);
    return ['success' => true];
  }

}
