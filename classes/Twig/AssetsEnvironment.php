<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

namespace PrestaShop\Module\AutoUpgrade\Twig;

use Symfony\Component\HttpFoundation\Request;

class AssetsEnvironment
{
    const DEV_BASE_URL = 'http://localhost:5173';

    /** @var string */
    private $shopBasePath;

    public function __construct(string $shopBasePath)
    {
        $this->shopBasePath = $shopBasePath;
    }

    public function isDevMode(): bool
    {
        return !empty($_ENV['AUTOUPGRADE_DEV_WATCH_MODE']) && $_ENV['AUTOUPGRADE_DEV_WATCH_MODE'] === '1';
    }

    public function getAssetsBaseUrl(Request $request): string
    {
        if ($this->isDevMode()) {
            return self::DEV_BASE_URL;
        }

        return $this->getShopUrlFromRequest($request) . '/modules/autoupgrade/views';
    }

    private function getShopUrlFromRequest(Request $request): string
    {
        // Determine the subdirectories of the PHP entry point (the script being executed)
        // relative to the shop root folder.
        // This calculation helps generate a base path that correctly accounts for any subfolder in which
        // the shop might be installed.
        $subDirs = explode(
            DIRECTORY_SEPARATOR,
            trim(
                str_replace(
                    $this->shopBasePath,
                    '',
                    dirname($request->server->get('SCRIPT_FILENAME', '')
                )
            ), DIRECTORY_SEPARATOR)
        );
        $numberOfSubDirs = count($subDirs);

        $path = explode('/', $request->getBasePath());

        $path = array_splice($path, 0, -$numberOfSubDirs);

        return implode('/', $path);
    }
}
