<?php

namespace mutation\filecache;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterCacheOptionsEvent;
use craft\helpers\ElementHelper;
use craft\services\Elements;
use craft\utilities\ClearCaches;
use craft\web\Application;
use craft\web\Response;
use craft\web\twig\variables\CraftVariable;
use mutation\filecache\models\SettingsModel;
use mutation\filecache\services\FileCacheService;
use mutation\filecache\variables\FileCacheVariable;
use mutation\translate\services\MessagesService;
use yii\base\Event;

class FileCachePlugin extends Plugin
{
	/**
	 * @var FileCachePlugin
	 */
	public static $plugin;

	private $_deleteCaches = false;

	public function init()
	{
		parent::init();

		self::$plugin = $this;

		$this->setComponents(
			[
				'fileCache' => FileCacheService::class,
			]
		);

		$this->registerCache();

		if ($this->isInstalled && !Craft::$app->request->getIsConsoleRequest()) {
			$this->initEvents();
		}
	}

	public function fileCacheService(): FileCacheService
	{
		return $this->fileCache;
	}

	protected function createSettingsModel(): ?Model
	{
		return new SettingsModel();
	}

	private function registerCache()
	{
		Event::on(
			ClearCaches::class,
			ClearCaches::EVENT_REGISTER_CACHE_OPTIONS,
			function (RegisterCacheOptionsEvent $event) {
				$event->options[] = array(
					'key' => 'file-caches',
					'label' => Craft::t('filecache', 'File caches'),
					'action' => [FileCachePlugin::$plugin->fileCacheService(), 'deleteAllFileCaches']
				);
			}
		);
	}

	private function initEvents()
	{
		Event::on(Application::class, Application::EVENT_INIT, [$this, 'handleApplicationInit']);

		Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, [$this, 'handleElementChange']);
		Event::on(Elements::class, Elements::EVENT_AFTER_RESAVE_ELEMENT, [$this, 'handleElementChange']);
		Event::on(Elements::class, Elements::EVENT_AFTER_RESTORE_ELEMENT, [$this, 'handleElementChange']);
		Event::on(Elements::class, Elements::EVENT_AFTER_DELETE_ELEMENT, [$this, 'handleElementChange']);
		Event::on(Elements::class, Elements::EVENT_AFTER_UPDATE_SLUG_AND_URI, [$this, 'handleElementChange']);

		if (Craft::$app->plugins->isPluginEnabled('translations-admin')) {
			Event::on(
				MessagesService::class,
				MessagesService::EVENT_AFTER_SAVE_MESSAGES,
				[$this, 'handleTranslationsChange']
			);
			Event::on(
				MessagesService::class,
				MessagesService::EVENT_AFTER_ADD_MESSAGE,
				[$this, 'handleTranslationsChange']
			);
			Event::on(
				MessagesService::class,
				MessagesService::EVENT_AFTER_DELETE_MESSAGES,
				[$this, 'handleTranslationsChange']
			);
		}

		Event::on(
			CraftVariable::class,
			CraftVariable::EVENT_INIT,
			function (Event $event) {
				/** @var CraftVariable $variable */
				$variable = $event->sender;
				$variable->set('filecache', FileCacheVariable::class);
			}
		);
	}

	public function handleApplicationInit()
	{
		$this->fileCacheService()->serveCache();

		Event::on(Response::class, Response::EVENT_AFTER_PREPARE, [$this, 'handleResponseAfterPrepare']);
	}

	public function handleResponseAfterPrepare(Event $event)
	{
		/** @var Response $response */
		$response = $event->sender;

		$this->fileCacheService()->writeCache($response);

		$this->fileCacheService()->replaceVariables($response);
	}

	public function handleElementChange(Event $event)
	{
		$settings = $this->getSettings();

		if (!$settings->cacheEnabled || Craft::$app->getConfig()->getGeneral()->devMode) {
			return;
		}

		$element = $event->element;

		if ($element === null) {
			return;
		}

		if (ElementHelper::isDraftOrRevision($element)) {
			return;
		}

		$this->_deleteCaches = true;
		Craft::$app->getResponse()->on(Response::EVENT_AFTER_PREPARE, [$this, 'handleResponse']);
	}

	public function handleTranslationsChange()
	{
		$this->_deleteCaches = true;
		Craft::$app->getResponse()->on(Response::EVENT_AFTER_PREPARE, [$this, 'handleResponse']);
	}

	public function handleResponse()
	{
		/** @var SettingsModel $settings */
		$settings = $this->getSettings();

		if (!$settings->cacheEnabled || Craft::$app->getConfig()->getGeneral()->devMode) {
			return;
		}

		if ($this->_deleteCaches) {
			$this->fileCacheService()->deleteAllFileCaches();

			$this->_deleteCaches = false;
		}
	}
}
