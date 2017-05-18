<?php namespace Castiron\WebpackAssets\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Theme;

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
     * @param string $manifestFilename
     * @param string $manifestClass
     * @param string $prop can be 'css' or 'js' currently
     * @return array
     */
    protected function getFiles($prop = '', $manifestFilename = 'assets-manifest', $manifestClass = 'WebpackBuiltFiles') {
        if (!$prop) {
            return [];
        }

        $this->loadAssetsManifest($manifestFilename);

        /**
         * Bail if we couldn't load the class
         */
        if (!class_exists($manifestClass)) {
            return [];
        }

        $propName = "${prop}Files";
        return $this->prependAssetsFolder(
            $manifestClass::$$propName
        );
    }

    /**
     * @param array $filenames
     * @return array
     */
    protected function prependAssetsFolder($filenames = []) {
        return array_map(function ($item) {
            return $this->assetsFolder() . DIRECTORY_SEPARATOR . $item;
        }, $filenames);
    }

    /**
     * @param $manifestFilename
     * @return string
     */
    protected function assetManifestPath($manifestFilename) {
        $path = [
            $this->publicFolder(),
            $this->assetsFolder(),
            $manifestFilename . '.php',
        ];
        return public_path(
            implode(DIRECTORY_SEPARATOR, $path)
        );
    }

    /**
     * @param $manifestFilename
     */
    protected function loadAssetsManifest($manifestFilename) {
        $file = $this->assetManifestPath($manifestFilename);
        if (!is_file($file)) {
            return;
        }
        require_once($file);
    }
}
