<?php

namespace mutation\filecache\models;

use craft\base\Model;

class SettingsModel extends Model
{
    /**
     * @var bool
     */
    public $cacheEnabled = true;

    /**
     * @var string
     */
    public $cacheFolderPath = 'web/filecache';

    /**
     * @var mixed
     */
    public $excludedUriPatterns = [];
}