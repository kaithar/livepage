<?php
/*
 * The purpose of this file is to prevent the site from doing anything if the database is out of date.
 * This is to keep the code from accidentally damaging the database.
 * Please keep this file in sync with what is in install.php and upgrade.php.
 */

if ($site_config['db_revision'] != 10)
{
  die("Please wait.  Upgrade in progess!<br/>If this message remains for an extended period, please contact the admins.");
}