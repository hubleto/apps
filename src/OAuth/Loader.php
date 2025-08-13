<?php

namespace HubletoApp\Community\OAuth;

class Loader extends \HubletoMain\App
{

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^oauth\/authorize\/?$/' => Controllers\Authorize::class,
      '/^oauth\/token\/?$/' => Controllers\Token::class,
    ]);

    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => 'OAuth', // or $this->translate('OAuth')
      'icon' => 'fas fa-table',
      'url' => 'settings/oauth',
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->load(Models\AccessToken::class)->dropTableIfExists()->install();
      $this->main->load(Models\AuthCode::class)->dropTableIfExists()->install();
      $this->main->load(Models\Client::class)->dropTableIfExists()->install();
      $this->main->load(Models\RefreshToken::class)->dropTableIfExists()->install();
      $this->main->load(Models\Scope::class)->dropTableIfExists()->install();
    }
    if ($round == 1) {
      $mClient = $this->main->load(Models\Client::class);
      $mClient->record->recordCreate([
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'name' => 'test client',
        'redirect_uri' => 'test_redirect_uri',
      ]);
    }
  }

}
