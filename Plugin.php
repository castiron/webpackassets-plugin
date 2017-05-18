<?php namespace Castiron\WebpackAssets;

use Castiron\WebpackAssets\Components\WebpackAssets;
use System\Classes\PluginBase;

/**
 * WebpackPhpManifest Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'WebpackPhpManifest',
            'description' => 'Include CSS and JS assets built using the nodejs module "php-manifest-webpack-plugin"',
            'author'      => 'Gabe Blair <gabe@castironcoding.com>',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            WebpackAssets::class => 'webpackAssets',
        ];
    }
}
