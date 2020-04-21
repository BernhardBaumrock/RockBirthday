<?php namespace ProcessWire;
/**
 * ProcessWire module to show a happy birthday message within a given period after birthday
 *
 * @author Bernhard Baumrock, 21.04.2020
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class RockBirthday extends WireData implements Module {

  public static function getModuleInfo() {
    return [
      'title' => 'RockBirthday',
      'version' => '1.0.0',
      'summary' => 'ProcessWire module to show a happy birthday message within a given period after birthday',
      'autoload' => "template=admin",
      'singular' => true,
      'icon' => 'smile-o',
      'requires' => [
        'ProcessWire>=3.0.133', // needs $page->meta()
      ],
    ];
  }

  public function ready() {
    $this->setConfig();
    $this->showHappyBirthday();
  }

  public function showHappyBirthday() {
    // get user's birthday
    $birthdate = $this->getBirthdate();
    if(!$birthdate) return;

    // check when the message was shown for the last time
    $user = $this->user;
    $shown = $user->meta('RockBirthday') ?: 0;
    if($this->diff($shown) < $this->maxDays) return;

    // get birthdate this and last year
    $thisYear = new \DateTime(date("Y").date("-m-d", $birthdate));
    $lastYear = new \DateTime((date("Y")-1).date("-m-d", $birthdate));

    // calc day diff from now to birthday
    $diff = $this->diff($thisYear);
    if($diff < 0) $diff = $this->diff($lastYear);
    if($diff > $this->maxDays) return;

    // show message
    $this->wire('modules')->get('JqueryUI')->use('vex');
    $this->addHookAfter('AdminTheme::getExtraMarkup', function($event) {
      $markup = $this->getMarkup();
      if(!$markup) return;

      $user = $this->user;
      $user->meta("RockBirthday", time());
      
      $data = $event->return;
      $data['footer'] .= $markup;
      $event->return = $data;
    });
  }

  public function diff($date) {
    if(is_int($date)) $date = new \DateTime(date("Y-m-d", $date));
    $now = new \DateTime(date("Y-m-d"));
    return -1*$now->diff($date)->format("%r%a");
  }

  /**
   * Hookable method to set the default config
   */
  public function ___setConfig() {
    $this->maxDays = 14;
  }

  /**
   * Get timestamp of birth date
   * @return int
   */
  public function ___getBirthdate() {}

  /**
   * Hookable method to get the message markup injected into the page footer
   * @return string
   */
  public function ___getMarkup() {
    $user = $this->user;
    $html = "Foo Bar <strong>{$user->name}</strong>";
    return "<script>vex.open({unsafeContent: \"$html\"});</script>";
  }
}
