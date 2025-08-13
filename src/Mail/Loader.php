<?php

namespace HubletoApp\Community\Mail;

class Loader extends \HubletoMain\App
{
  public bool $hasCustomSettings = true;

  public function __construct(\HubletoMain\Loader $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^mail\/?$/' => Controllers\Mailboxes::class,
      '/^mail\/accounts\/?$/' => Controllers\Accounts::class,
      '/^mail\/mails\/(?<idMailbox>\d+)\/?$/' => Controllers\Mails::class,


      '/^mail\/inbox\/?$/' => Controllers\Inbox::class,
      '/^mail\/sent\/?$/' => Controllers\Sent::class,
      '/^mail\/drafts\/?$/' => Controllers\Drafts::class,
      '/^mail\/outbox\/?$/' => Controllers\Outbox::class,
      '/^mail\/templates\/?$/' => Controllers\Templates::class,
      '/^mail\/all\/?$/' => Controllers\All::class,
      '/^mail\/settings\/?$/' => Controllers\Settings::class,
      '/^mail\/api\/mark-as-read\/?$/' => Controllers\Api\MarkAsRead::class,
      '/^mail\/api\/mark-as-unread\/?$/' => Controllers\Api\MarkAsUnread::class,
    ]);

    $appMenu = $this->main->di->create(\HubletoApp\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'mail', $this->translate('Mail'), 'fas fa-envelope');
    $appMenu->addItem($this, 'mail/accounts', $this->translate('Accounts'), 'fas fa-file-import');

    $this->main->crons->addCron(Crons\GetMails::class);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->di->create(Models\Account::class)->dropTableIfExists()->install();
      $this->main->di->create(Models\Mailbox::class)->dropTableIfExists()->install();
      $this->main->di->create(Models\Mail::class)->dropTableIfExists()->install();
      $this->main->di->create(Models\Index::class)->dropTableIfExists()->install();
    }
  }

  public function getNotificationsCount(): int
  {
    $mMail = $this->main->di->create(\HubletoApp\Community\Mail\Models\Mail::class);
    return $mMail->record->prepareReadQuery()
      ->where(function ($q) {
        $q->where('midx.id_to', $this->main->auth->getUserId());
        $q->orWhere('midx.id_cc', $this->main->auth->getUserId());
        $q->orWhere('midx.id_bcc', $this->main->auth->getUserId());
      })
      ->whereNull('datetime_read')
      ->count()
    ;
  }

  public function parseEmailsFromString(string $emails): array
  {
    $emailsFound = [];
    $emails = str_replace(' ', '', $emails);
    $emails = str_replace(';', ',', $emails);
    foreach (explode(',', $emails) as $email) {
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailsFound[] = $email;
      }
    }

    return $emailsFound;
  }

  public function getCipherKey(): string
  {
    $key = $this->configAsString('cipherKey');
    if (empty($key)) {
      $this->saveConfig('cipherKey', openssl_random_pseudo_bytes(64));
      $key = $this->configAsString('cipherKey');
    }
    return $key;
  }

  public function send(
    int|string $to,
    int|string $cc,
    int|string $bcc,
    string $subject,
    string $body,
    string $color = '',
    int $priority = 0
  ): array {
    $user = $this->main->auth->getUser();
    $idUser = $user['id'] ?? 0;
    $fromEmail = $user['email'] ?? '';

    $mUser = $this->main->di->create(\HubletoApp\Community\Settings\Models\User::class);
    $users = $mUser->record->get()->toArray();
    $usersByEmail = [];
    $emailsByUserId = [];
    foreach ($users as $user) {
      $usersByEmail[$user['email']] = $user['id'];
      $emailsByUserId[$user['id']] = $user['email'];
    }

    $mail = [];

    if (!empty($fromEmail)) {

      if (is_string($to)) {
        $toEmails = $this->parseEmailsFromString($to);
      } else {
        $toEmails = [ $emailsByUserId[$to] ];
      }

      if (is_string($cc)) {
        $ccEmails = $this->parseEmailsFromString($cc);
      } else {
        $ccEmails = [ $emailsByUserId[$cc] ];
      }

      if (is_string($bcc)) {
        $bccEmails = $this->parseEmailsFromString($bcc);
      } else {
        $bccEmails = [ $emailsByUserId[$bcc] ];
      }

      $mMail = $this->main->di->create(Models\Mail::class);
      $mIndex = $this->main->di->create(Models\Index::class);

      $mailData = [
        'priority' => $priority,
        'sent' => date('Y-m-d H:i:s'),
        'from' => $fromEmail,
        'to' => join(', ', $toEmails),
        'cc' => join(', ', $ccEmails),
        'subject' => $subject,
        'body' => $body,
        'color' => $color,
      ];

      $mail = $mMail->record->create($mailData)->toArray();
      $idMail = $mail['id'] ?? 0;

      if ($idMail > 0) {
        foreach ($toEmails as $email) {
          $idUserTo = $usersByEmail[$email] ?? 0;
          if ($idUserTo > 0) {
            $mIndex->record->create(['id_mail' => $idMail, 'id_from' => $idUser, 'id_to' => $idUserTo]);
          }
        }
        foreach ($ccEmails as $email) {
          $idUserCc = $usersByEmail[$email] ?? 0;
          if ($idUserCc > 0) {
            $mIndex->record->create(['id_mail' => $idMail, 'id_from' => $idUser, 'id_cc' => $idUserCc]);
          }
        }
        foreach ($bccEmails as $email) {
          $idUserBcc = $usersByEmail[$email] ?? 0;
          if ($idUserBcc > 0) {
            $mIndex->record->create(['id_mail' => $idMail, 'id_from' => $idUser, 'id_bcc' => $idUserBcc]);
          }
        }
      }
    }

    return $mail;
  }
}
