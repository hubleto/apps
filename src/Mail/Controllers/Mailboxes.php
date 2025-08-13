<?php

namespace HubletoApp\Community\Mail\Controllers;

use HubletoApp\Community\Mail\Models\Mailbox;

class Mailboxes extends \HubletoMain\Controller
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

    $mMailbox = new Mailbox($this->main);

    $this->viewParams['mailboxes'] = $mMailbox->record->prepareReadQuery()->get()->toArray();

    $this->setView('@HubletoApp:Community:Mail/Mailboxes.twig');
  }

}
