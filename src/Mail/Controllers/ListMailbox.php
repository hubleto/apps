<?php

namespace HubletoApp\Community\Mail\Controllers;

use HubletoApp\Community\Mail\Models\Mailbox;

class ListMailbox extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'mail', 'content' => $this->translate('Mail') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $idMailbox = $this->main->urlParamAsInteger('idMailbox');
    $mMailbox = new Mailbox($this->main);

    $this->viewParams['mailbox'] = $mMailbox->record->find($idMailbox)?->toArray();

    $this->setView('@HubletoApp:Community:Mail/ListMailbox.twig');
  }

}
