<?php
namespace Pragma\Docs\Helpers;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Migrate{
	public static function preUpdate(Event $event) {
		if (!self::checkConfig($event)) {
			die();
		}
	}

	public static function postUpdate(Event $event){
		if (self::checkConfig($event)) {
			// Run phinx migrate
			self::phinxMigrate($event);
		} else {
			die();
		}
	}

	public static function postInstall(Event $event){
		if (self::checkConfig($event)) {
			// Run phinx migrate
			self::phinxMigrate($event,true);
		} else {
			die();
		}
	}

	protected static function checkConfig(Event &$event) {
		if(!file_exists('config/config.php')){
			$event->getIO()->writeError(array(
				"You need to configure your app.",
				"Create config/config.php and define database informations connection.",
				"And re-run composer install/update."
			));
			$event->stopPropagation();
			return false;
		}else{
			return true;
		}
	}

	protected static function phinxMigrate(Event &$event,$install = false){
		$phinxApp = require 'vendor/robmorgan/phinx/app/phinx.php';
		$phinxTextWrapper = new \Phinx\Wrapper\TextWrapper($phinxApp);

		$phinxTextWrapper->setOption('configuration', __DIR__.'/../../../phinx.php');
		$phinxTextWrapper->setOption('parser', 'PHP');
		$phinxTextWrapper->setOption('environment', 'default');

		$log = $phinxTextWrapper->getMigrate();
		$event->getIO()->write($log);
		if ($install) {
			$log = $phinxTextWrapper->getSeed();
			$event->getIO()->write($log);
		}
	}
}