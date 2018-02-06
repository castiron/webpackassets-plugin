<?php namespace Castiron\WebpackAssets\Components;

use Castiron\WebpackAssets\Services\ManifestLoader;
use Cms\Classes\ComponentBase;
use Cms\Classes\Theme;
use October\Rain\Exception\ApplicationException;

/**
 * Class WebpackAssets
 * @package Castiron\WebpackAssets\Components
 */
class WebpackAssets extends ComponentBase {
    /**
     * @return array
     */
    public function componentDetails() {
        return [
            'name'        => 'Webpack assets',
            'description' => 'Render javascript/CSS includes for webpack assets'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties(){

        return [
            'assetsFolder' => [
                'title'       => 'Assets folder',
                'description' => 'Example: "assets". The assets folder name where your resources are written by webpack',
                'type'        => 'string',
                'default'     => 'assets',
            ],
            'publicFolder' => [
                'title'       => 'Public folder',
                'description' => 'Example: "www". the public folder (if you are using one). E.g. "public," or "www." See https://octobercms.com/docs/setup/configuration#public-folder',
                'type'        => 'string',
                'default'     => '',
            ]
        ];
    }

    /**
     * @param string $manifestFilename The name of the php file that was written by webpack
     * @param string $manifestClass The class name used to scope the asset files
     * @return string
     */
    public function css($manifestFilename = 'assets-manifest', $manifestClass = 'WebpackBuiltFiles') {
        $files = $this->getFiles('css', $manifestFilename, $manifestClass);
        return implode("\n", array_map(function ($file) {
            return '<link rel="stylesheet" href="' . url($file). '">';
        }, $files));
    }

    /**
     * @param string $manifestFilename The name of the php file that was written by webpack
     * @param string $manifestClass The class name used to scope the asset files
     * @return string
     */
    public function js($manifestFilename = 'assets-manifest', $manifestClass = 'WebpackBuiltFiles') {
        $files = $this->getFiles('js', $manifestFilename, $manifestClass);
        return implode("\n", array_map(function ($file) {
            return '<script src="' . url($file). '"></script>';
        }, $files));
    }

    /**
     * @return string
     */
    protected function assetsFolder() {
        return rtrim($this->property('assetsFolder'), DIRECTORY_SEPARATOR);
    }

    /**
     * @return string
     */
    protected function publicFolder() {
        return rtrim($this->property('publicFolder'), DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $prop can be 'css' or 'js' currently
     * @param string $manifestFilename
     * @param string $manifestClass
     * @return array
     * @throws ApplicationException
     */
    protected function getFiles($prop = '', $manifestFilename, $manifestClass = 'WebpackBuiltFiles') {
        if (!$prop) {
            return [];
        }

        // Replace with call to loader class,
        // and pass public and assets folder
        $this->loadAssetsManifest($manifestFilename);

        /**
         * Bail if we couldn't load the class
         */
        if (!class_exists($manifestClass)) {
            throw new ApplicationException('Could not load class ' . $manifestClass . ' from asset manifest file ' .
                $this->manifestLoader->assetManifestPath($manifestFilename)
            );
        }

        $assetListVar = "${prop}Files";
        return $manifestClass::$$assetListVar;
    }

    /**
     * @param $manifestFilename
     * @throws ApplicationException
     */
    protected function loadAssetsManifest($manifestFilename) {
        (new ManifestLoader(
            $this->publicFolder(),
            $this->assetsFolder()
        ))->load($manifestFilename);
    }
}
