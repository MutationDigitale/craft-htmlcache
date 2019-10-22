<?php

namespace mutation\filecache\console\controllers;

use Craft;
use craft\helpers\Console;
use mutation\filecache\FileCachePlugin;
use mutation\filecache\models\SettingsModel;
use yii\console\Controller;

class CacheController extends Controller
{
	public function actionWarm(): void
	{
		/** @var SettingsModel $settings */
		$settings = FileCachePlugin::$plugin->getSettings();

		if (!$settings->cacheEnabled || Craft::$app->getConfig()->getGeneral()->devMode) {
			$this->stderr(Craft::t('filecache', 'File cache is disabled.') . PHP_EOL, Console::FG_RED);
			return;
		}

		$this->stdout(Craft::t('filecache', 'Warming file cache.') . PHP_EOL);

		FileCachePlugin::$plugin->fileCacheService()->warmAllCache();

		$this->stdout(Craft::t('filecache', 'File cache successfully warmed.') . PHP_EOL, Console::FG_GREEN);
	}
}
