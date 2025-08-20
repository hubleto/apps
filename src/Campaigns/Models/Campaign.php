<?php

namespace HubletoApp\Community\Campaigns\Models;

use HubletoApp\Community\Campaigns\Lib;

use HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Mail\Models\Mail;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\DateTime;

class Campaign extends \Hubleto\Framework\Models\Model
{
  public string $table = 'campaigns';
  public string $recordManagerClass = RecordManagers\Campaign::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id'],
    'LEADS' => [ self::HAS_MANY, CampaignLead::class, 'id_lead', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setProperty('defaultVisibility', true),
      'utm_source' => (new Varchar($this, $this->translate('UTM source')))->setProperty('defaultVisibility', true),
      'utm_campaign' => (new Varchar($this, $this->translate('UTM campaign')))->setProperty('defaultVisibility', true),
      'utm_term' => (new Varchar($this, $this->translate('UTM term')))->setProperty('defaultVisibility', true),
      'utm_content' => (new Varchar($this, $this->translate('UTM content')))->setProperty('defaultVisibility', true),
      'target_audience' => (new Text($this, $this->translate('Target audience')))->setProperty('defaultVisibility', true),
      'goal' => (new Text($this, $this->translate('Goal')))->setProperty('defaultVisibility', true),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'mail_body' => (new Text($this, $this->translate('Mail body (HTML)')))->setReactComponent('InputWysiwyg'),
      'color' => (new Color($this, $this->translate('Color'))),
      'id_mail_template' => (new Lookup($this, $this->translate('Mail template'), Mail::class))->setProperty('defaultVisibility', true),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setProperty('defaultVisibility', true)->setDefaultValue($this->main->auth->getUserId())->setProperty('defaultVisibility', true),
      'datetime_created' => (new DateTime($this, $this->translate('Created')))->setProperty('defaultVisibility', true)->setRequired()->setDefaultValue(date('Y-m-d H:i:s')),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add Campaign');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    $mMail = $this->main->load(Mail::class);
    $template = $mMail->record->find((int) $savedRecord['id_mail_template'])->toArray();
    $bodyHtml = Lib::addUtmVariablesToEmailLinks(
      (string) $template['body_html'],
      (string) $savedRecord['utm_source'],
      (string) $savedRecord['utm_campaign'],
      (string) $savedRecord['utm_term'],
      (string) $savedRecord['utm_content'],
    );

    $mCampaign = $this->main->load(Campaign::class);
    $mCampaign->record->find((int) $savedRecord['id'])->update([
      'mail_body' => $bodyHtml
    ]);

    return $savedRecord;
  }

}
