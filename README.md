# RockBirthday

## A message to Russian 🇷🇺 people

If you currently live in Russia, please read [this message](https://github.com/Roave/SecurityAdvisories/blob/latest/ToRussianPeople.md).

[![SWUbanner](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)

---

ProcessWire module to show a happy birthday message within a given period after birthday.

See instructions here: https://weekly.pw/issue/311/#new-module-rockbirthday

## Usage

Just install the module and customize it via hooks in `/site/init.php` (**not ready.php**):

```php
// dont show message after set maxDays
$wire->addHookAfter("RockBirthday::setConfig", function($event) {
  $event->object->maxDays = 30; // 14 is default
});

// get date of birth of user
// must return a timestamp (int)
$wire->addHookAfter("RockBirthday::getBirthdate", function($event) {
  $user = $this->user;
  $event->return = $user->getUnformatted('your_birthdate_field');
});

// get markup of message
$wire->addHookAfter("RockBirthday::getMarkup", function($event) {
  $user = $this->user;
  $html = "<h1>Happy Birthday, {$user->name}!</h1>";
  $event->return = "<script>vex.open({unsafeContent: \"$html\"});</script>";
});
