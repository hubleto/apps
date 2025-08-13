<?php

namespace HubletoApp\Community\Mail\Controllers;

class Settings extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'mail', 'content' => $this->translate('Mail') ],
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $settingsChanged = $this->main->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $smtpHost = $this->main->urlParamAsString('smtpHost');
      $this->main->apps->community('Mail')->setConfigAsString('smtpHost', $smtpHost);
      $this->main->apps->community('Mail')->saveConfig('smtpHost', $smtpHost);

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@HubletoApp:Community:Mail/Settings.twig');
  }

}
