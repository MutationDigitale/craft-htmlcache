<?php

namespace mutation\filecache\services;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use craft\elements\User;
use craft\helpers\App;
use craft\helpers\FileHelper;
use craft\helpers\Html;
use craft\helpers\StringHelper;
use craft\web\Response;
use mutation\filecache\FileCachePlugin;
use mutation\filecache\models\SettingsModel;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use function in_array;

class FileCacheService extends Component
{
	public function writeCache(Response $response)
	{
		if (!$this->isCacheableRequest()) {
			return;
		}

		Craft::$app->getCache()->add($this->getCacheKey(), trim($response->content));
	}

	public function serveCache()
	{
		if (!$this->isCacheableRequest()) {
			return;
		}

		$cachedContent = Craft::$app->getCache()->get($this->getCacheKey());

		if (!$cachedContent) {
			return;
		}

		$response = Craft::$app->response;

		$response->content = $cachedContent;

		$this->replaceVariables($response);

		$response->send();
		exit();
	}

	public function replaceVariables(Response $response)
	{
		$this->replaceCsrfInput($response);
		$this->replaceJsCrsfToken($response);
	}

	public function deleteAllFileCaches()
	{
		Craft::$app->getCache()->flush();
	}

	private function isCacheableRequest(): bool
	{
		/** @var SettingsModel $settings */
		$settings = FileCachePlugin::$plugin->getSettings();

		if (!$settings->cacheEnabled || Craft::$app->getConfig()->getGeneral()->devMode) {
			return false;
		}

		$request = Craft::$app->getRequest();
		$response = Craft::$app->getResponse();

		if (!$request->getIsSiteRequest() ||
			!$request->getIsGet() ||
			$request->getIsConsoleRequest() ||
			$request->getIsActionRequest() ||
			$request->getIsPreview() ||
			$request->getIsAjax() ||
			$request->getToken() ||
			!$response->getIsOk()) {
			return false;
		}

		// Don't cache JSON requests
		if (is_array($response->data)) {
			return false;
		}

		/** @var User|null $user */
		$user = Craft::$app->getUser()->getIdentity();
		if ($user !== null) {
			if (!Craft::$app->getIsLive() && !$user->can('accessSiteWhenSystemIsOff')) {
				return false;
			}
			if ($user->getPreference('enableDebugToolbarForSite')) {
				return false;
			}
			if (Craft::$app->plugins->isPluginEnabled('admin-bar') &&
				(Craft::$app->getUser()->getIsAdmin() || Craft::$app->getUser()->checkPermission('accessCp'))) {
				return false;
			}
		}

		// Return false if there is still image transforms to be done
		if (StringHelper::contains(stripslashes($response->data), 'assets/generate-transform')) {
			return false;
		}

		// Check if an element is matched or if a matched entry is in excluded sections, entry types and sites
		if (!$this->isCacheableElement()) {
			return false;
		}

		return true;
	}

	private function isCacheableElement(): bool
	{
		$element = Craft::$app->urlManager->getMatchedElement();

		if ($element === false) {
			return false;
		}

		/** @var SettingsModel $settings */
		$settings = FileCachePlugin::$plugin->getSettings();

		if (is_a($element, craft\elements\Entry::class)) {
			/** @var Entry $element */
			$entry = $element;

			if (in_array($entry->section->handle, $settings->excludedEntrySections, true)) {
				return false;
			}

			if (in_array($entry->type->handle, $settings->excludedEntryTypes, true)) {
				return false;
			}

			if (in_array($entry->site->handle, $settings->excludedSites, true)) {
				return false;
			}
		}

		return true;
	}

	private function replaceCsrfInput(Response $response)
	{
		/** @var SettingsModel $settings */
		$settings = FileCachePlugin::$plugin->getSettings();

		$request = Craft::$app->getRequest();

		if (!is_string($response->content) ||
			strpos($response->content, $settings->csrfInputKey) === false) {
			return;
		}

		$response->content = str_replace(
			$settings->csrfInputKey,
			Html::hiddenInput($request->csrfParam, $request->getCsrfToken()),
			$response->content
		);
	}

	private function replaceJsCrsfToken(Response $response)
	{
		/** @var SettingsModel $settings */
		$settings = FileCachePlugin::$plugin->getSettings();

		$request = Craft::$app->getRequest();

		if (!is_string($response->content) ||
			strpos($response->content, $settings->csrfJsTokenKey) === false) {
			return;
		}

		$csrfParam = $request->csrfParam;
		$csrfToken = $request->getCsrfToken();

		$script = <<<HTML
<script>
	window.$csrfParam = "$csrfToken";
</script>
HTML;

		$response->content = str_replace(
			$settings->csrfJsTokenKey,
			$script,
			$response->content
		);
	}

	private function getCacheKey(): array
	{
		return ['MUTATION_FILE_CACHE', Craft::$app->sites->getCurrentSite()->handle, Craft::$app->request->getPathInfo(true)];
	}
}
