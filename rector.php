<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.template_boilerplate
 *
 * @copyright   (C) 2024. All rights reserved by Jörg Schöneburg
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;
use Rector\Php81\Rector\Array_\ArrayToFirstClassCallableRector;

$extensionSrcPaths = array_merge(
    glob(__DIR__ . '/../mod_*/src') ?: [],
    glob(__DIR__ . '/../plg_*/src') ?: [],
);

return RectorConfig::configure()
    ->withPaths(array_merge([__DIR__ . '/src'], $extensionSrcPaths))
    ->withSkip([
        __DIR__ . '/src/vendor',
        '*/vendor/*',
        '*/node_modules/*',
        ChangeSwitchToMatchRector::class,
        ArrayToFirstClassCallableRector::class,
    ])
    ->withPhpSets(php81: true)
    ->withPreparedSets(
        deadCode: false,
        codeQuality: false,
        codingStyle: false,
        typeDeclarations: false,
        privatization: false,
        naming: false,
        instanceOf: false,
        earlyReturn: false,
    );
