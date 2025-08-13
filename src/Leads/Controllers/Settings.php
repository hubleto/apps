<?php

namespace HubletoApp\Community\Leads\Controllers;

class Settings extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'leads', 'content' => $this->translate('Leads') ],
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $settingsChanged = $this->main->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $calendarColor = $this->main->urlParamAsString('calendarColor');
      $this->main->apps->community('Leads')->setConfigAsString('calendarColor', $calendarColor);
      $this->main->apps->community('Leads')->saveConfig('calendarColor', $calendarColor);

      $leadPrefix = $this->main->urlParamAsString('leadPrefix');
      $this->main->apps->community('Leads')->setConfigAsString('leadPrefix', $leadPrefix);
      $this->main->apps->community('Leads')->saveConfig('leadPrefix', $leadPrefix);

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@HubletoApp:Community:Leads/Settings.twig');
  }

}
