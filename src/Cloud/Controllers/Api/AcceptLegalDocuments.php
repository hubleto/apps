<?php

namespace HubletoApp\Community\Cloud\Controllers\Api;

class AcceptLegalDocuments extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $this->main->config->saveForUser('legalDocumentsAccepted', date('Y-m-d H:i:s'));
    $this->main->router->redirectTo('');
  }

}
