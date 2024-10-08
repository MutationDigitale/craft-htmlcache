# Changelog

## 4.0.0 - 2023-05-15

### Added
- Craft 5 support

## 3.0.1 - 2023-05-15

### Changed
- Do not cache ajax requests
- Take full path into account for cache key

## 3.0.0 - 2022-11-25

### Changed
- Use Craft/Yii caching mechanism

## 3.0.0-beta.2 - 2022-08-02

### Fixed
- Fixed empty HTML cache

## 3.0.0-beta.1 - 2022-08-02

### Added
- Initial Craft 4 support

## 2.4.1 - 2022-04-27

### Fixed
- Serve cache when application is initialized instead of after plugins loaded

## 2.4.0 - 2022-01-07

### Added
- Empty cache when saving translations from translations-admin plugin

## 2.3.6 - 2021-12-08

### Fixed
- Serve cache after all plugins are loaded

## 2.3.5 - 2021-09-17

### Fixed
- Don't cache requests with ?token= param

## 2.3.4 - 2020-04-29

### Fixed
- Don't cache JSON requests

## 2.3.3 - 2020-04-03

### Fixed
- Check if response data is a string before replacing csrf tokens

## 2.3.2 - 2020-04-03

### Fixed
- Don't serve cache if no element matched
- Don't replace csrf tokens if not in response data

## 2.3.1 - 2020-03-16

### Fixed
- Be able to clear cache in console

## 2.3.0 - 2019-12-20

### Changed
- Changed how the CSRF token variables are generated. They are replaced server side instead of client side.

### Removed
- Removed `craft.filecache.injectUrl('URL')`

## 2.2.1 - 2019-12-20

### Fixed
- Removed no longer working File Cache utility

## 2.2.0 - 2019-12-20

### Changed
- Store caching in `storage/runtime`
- Server static cache files from PHP instead of htaccess
- Use `FileHelper` class

### Removed
- Warming

## 2.1.7 - 2019-10-30

### Changed
- Fixed PHP 7.0 error

## 2.1.6 - 2019-10-30

### Changed
- Removed PHP 7.1 requirement

## 2.1.5 - 2019-10-22

### Added
- Take into account the admin bar plugin

### Fixed
- Fixed "Always disable cache when `devMode` is `true`"

## 2.1.4 - 2019-10-21

### Changed
- Always disable cache when `devMode` is `true`

## 2.1.3 - 2019-10-21

### Changed
- Fixed twig variable  `injectJsCsrfToken`

## 2.1.2 - 2019-10-21

### Changed
- Removed `injectJsCsrfToken` settings, call the twig variable  `injectJsCsrfToken` instead
- Do not automatically warm cache

## 2.1.1 - 2019-10-18

### Changed
- Prevent caching if `enableDebugToolbarForSite` is `true`

## 2.1.0 - 2019-10-18

### Changed
- Craft 3.3 necessary now
- Cache when logged in but not in preview
- Support drafts and revisions
- Support resave and restore events
- Formatting

## 2.0.7 - 2019-09-11

### Fixed
- Removed version from composer.json

## 2.0.6 - 2019-09-11

### Fixed
- Replaced getAlias by parseEnv for site url

## 2.0.5 - 2019-03-06

### Fixed
- Fixed bug with XMLHttpRequest on IE11

## 2.0.4 - 2019-02-11

### Fixed
- Formatting

## 2.0.3 - 2019-02-11

### Fixed
- Fixed an error in isCacheableRequest()

## 2.0.2 - 2019-02-11

### Fixed
- Fixed an error when getMatchedElement was returning false

## 2.0.1 - 2019-01-31

### Added
- Added EVENT_AFTER_UPDATE_SLUG_AND_URI event

## 2.0.0 - 2019-01-31

### Added
- Exclude sites from caching and warming

### Changed
- Removed template caches and simplified the process

### Removed
- Removed exclude uris

## 1.1.7 - 2019-01-30

### Fixed
- Fixed warm cache job spawning too many times

## 1.1.6 - 2019-01-22

### Fixed
- Fix "replace 'template-caches' instead of adding a new one"

## 1.1.5 - 2019-01-22

### Changed
- Replace 'template-caches' instead of adding a new one

## 1.1.4 - 2019-01-22

### Fixed
- Fixed an error if templatecache didn't exist in DB

## 1.1.3 - 2019-01-11

### Changed
- Replaced Template caches by Template and file caches in CP

## 1.1.2 - 2019-01-11

### Changed
- For injected elements, replace span instead of append

## 1.1.1 - 2019-01-11

### Changed
- More time reserved for the warming job

## 1.1.0 - 2019-01-11

### Added
- Added the ability to inject dynamic content and csrf
- Added the ability to exclude sections and entry types from warming

## 1.0.18 - 2019-01-09

### Changed
- Use craft cache events

## 1.0.17 - 2018-12-04

### Added
- Exclude entries by section or type
- Warm multiple urls at a time with Guzzle Pool

### Changed
- Cleanup

## 1.0.16 - 2018-11-27

### Fixed
- Don't cache request with user connected

## 1.0.15 - 2018-11-15

### Fixed
- Typo

## 1.0.14 - 2018-11-14

### Fixed
- Don't completly delete filecache folder

## 1.0.13 - 2018-11-07

### Added
- Added logs for warming cache

## 1.0.12 - 2018-11-07

### Fixed
- Error with homepage path not being index.html

## 1.0.11 - 2018-11-05

### Fixed
- Error with mkdir

## 1.0.10 - 2018-11-05

### Added
- Option automaticallyWarmCache

## 1.0.9 - 2018-11-05

### Added
- Console commands

## 1.0.8 - 2018-11-05

### Added
- Option to delete all file cache

## 1.0.7 - 2018-11-04

### Added
- Warm cache when cache is cleared

## 1.0.0 - 2018-11-02

### Added
- Initial release
