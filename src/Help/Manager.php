<?php

namespace HubletoApp\Community\Help;

class Manager extends \HubletoMain\CoreClass
{

  /** @var array<string, string> */
  public array $hotTips = [];

  /** @var array<string, array<string, string>> */
  public array $contextHelpUrls = [];

  public function addHotTip(string $slugRegExp, string $title): void
  {
    $this->hotTips[$slugRegExp] = $title;
  }

  public function addContextHelpUrls(string $slugRegExp, array $urls): void
  {
    $this->contextHelpUrls[$slugRegExp] = $urls;
  }

  public function getCurrentContextHelpUrls(string $slugRegExp): array
  {
    foreach ($this->contextHelpUrls as $regExp => $urls) {
      if (preg_match($regExp, $slugRegExp)) {
        return $urls;
      }
    }

    return [];
  }

}