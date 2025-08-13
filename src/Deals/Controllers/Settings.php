<?php

namespace HubletoApp\Community\Deals\Controllers;

class Settings extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'deals', 'content' => $this->translate('Deals') ],
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $settingsChanged = $this->main->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $calendarColor = $this->main->urlParamAsString('calendarColor');
      $this->main->apps->community('Deals')->setConfigAsString('calendarColor', $calendarColor);
      $this->main->apps->community('Deals')->saveConfig('calendarColor', $calendarColor);

      $dealPrefix = $this->main->urlParamAsString('dealPrefix');
      $this->main->apps->community('Deals')->setConfigAsString('dealPrefix', $dealPrefix);
      $this->main->apps->community('Deals')->saveConfig('dealPrefix', $dealPrefix);

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@HubletoApp:Community:Deals/Settings.twig');
  }

}
