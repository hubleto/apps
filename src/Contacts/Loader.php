<?php

namespace HubletoApp\Community\Contacts;

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
      '/^contacts\/?$/' => Controllers\Contacts::class,
      '/^contacts\/add\/?$/' => ['controller' => Controllers\Contacts::class, 'vars' => ['recordId' => -1]],
      '/^contacts(\/(?<recordId>\d+))?\/?$/' => Controllers\Contacts::class,
      '/^contacts\/get-customer-contacts\/?$/' => Controllers\Api\GetCustomerContacts::class,
      '/^contacts\/check-primary-contact\/?$/' => Controllers\Api\CheckPrimaryContact::class,
      '/^settings\/contact-tags\/?$/' => Controllers\Tags::class,
      '/^contacts\/categories\/?$/' => Controllers\Categories::class,
      '/^contacts\/import\/?$/' => Controllers\Import::class,
    ]);

    $this->main->apps->community('Settings')?->addSetting($this, ['title' => $this->translate('Contact Categories'), 'icon' => 'fas fa-phone', 'url' => 'settings/categories']);
    $this->main->apps->community('Settings')?->addSetting($this, [
      'title' => $this->translate('Contact Tags'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/contact-tags',
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mCategory = $this->main->load(Models\Category::class);
      $mContact = $this->main->load(Models\Contact::class);
      $mValue = $this->main->load(Models\Value::class);
      $mTag = $this->main->load(Models\Tag::class);
      $mContactTag = $this->main->load(Models\ContactTag::class);

      $mCategory->dropTableIfExists()->install();
      $mContact->dropTableIfExists()->install();
      $mValue->dropTableIfExists()->install();
      $mTag->dropTableIfExists()->install();
      $mContactTag->dropTableIfExists()->install();

      $mCategory->record->recordCreate([ 'name' => 'Work' ]);
      $mCategory->record->recordCreate([ 'name' => 'Home' ]);
      $mCategory->record->recordCreate([ 'name' => 'Other' ]);

      $mTag->record->recordCreate([ 'name' => "IT manager", 'color' => '#D33115' ]);
      $mTag->record->recordCreate([ 'name' => "CEO", 'color' => '#4caf50' ]);
      $mTag->record->recordCreate([ 'name' => "Desicion Maker", 'color' => '#fcc203' ]);
      $mTag->record->recordCreate([ 'name' => "Sales", 'color' => '#2196f3' ]);
      $mTag->record->recordCreate([ 'name' => "Support", 'color' => '#03fc8c' ]);
      $mTag->record->recordCreate([ 'name' => "Other", 'color' => '#b3b3b3' ]);
    }
  }

  /**
   * Implements fulltext search functionality for the contacts
   *
   * @param array $expressions List of expressions to be searched and glued with logical 'or'.
   * 
   * @return array
   * 
   */
  public function search(array $expressions): array
  {
    $mContact = $this->main->load(Models\Contact::class);
    $qContacts = $mContact->record->prepareReadQuery();
    
    foreach ($expressions as $e) {
      $qContacts = $qContacts->where(function($q) use ($e) {
        $q->orWhere('contacts.first_name', 'like', '%' . $e . '%');
        $q->orWhere('contacts.last_name', 'like', '%' . $e . '%');
      });
    }

    $contacts = $qContacts->get()->toArray();

    $results = [];

    foreach ($contacts as $contact) {
      $results[] = [
        "id" => $contact['id'],
        "label" => $contact['first_name'] . ' ' . $contact['last_name'],
        "url" => 'contacts/' . $contact['id'],
        "description" => $contact['date_created'],
      ];
    }

    return $results;
  }

}
