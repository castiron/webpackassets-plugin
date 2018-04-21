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
            'publicFolder' => [
                'title'       => 'Public folder',
                'description' => 'Example: "www". the public folder (if you are using one). E.g. "public," or "www." See https://octobercms.com/docs/setup/configuration#public-folder',
                'type'        => 'string',
                'default'     => 'www',
            ],
            'assetsFolder' => [
                'title'       => 'Assets folder',
                'description' => 'Example: "assets". The assets folder name where your resources are written by webpack',
                'type'        => 'string',
                'default'     => 'assets',
            ],
            'manifestFilename' => [
                'title'       => 'Manifest Filename',
                'description' => 'Name of manifest filename output by Webpack (default: "manifest.json")',
                'type'        => 'string',
                'default'     => 'manifest.json',
            ]
        ];
    }

    /**
     * @param string $manifestFilename The name of the php file that was written by webpack
     * @param string $manifestClass The class name used to scope the asset files
     * @return string
     */
    public function file($fileName) {
        return $this->getFile($fileName);
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
     * @param string $fileName
     * @return array
     * @throws ApplicationException
     */
    protected function getFile($fileName) {
        if (!$fileName) {
            return null;
        }

        // Replace with call to loader class,
        // and pass public and assets folder
        $manifest = (new ManifestLoader(
            $this->publicFolder(),
            $this->assetsFolder(),
            $this->property('manifestFilename')
        ))->getManifest();

        /**
         * Bail if we couldn't load the class
         */
        if (count($manifest) == 0) {
            throw new ApplicationException('Manifest file could not be loaded, or it contained no files: ' .
                $manifestFilename
            );
        }

        if (!array_key_exists ($fileName , $manifest)) return '';

        return $this->fileTag($manifest[$fileName]);
    }

    /**
     * @param string $filePath
     * @param string $fileType
     * @return string
     */
     protected function fileTag($filePath) {
         // Setup templates for tag types
         $tags = array(
             'css' => function($path) {
                return '<link rel="stylesheet" type="text/css" href="' . url($path) .'">';
             },
             'js' => function($path) {
                return '<script type="text/javascript" src="' . url($path) . '"></script>';
             }
         );

         // Figure out the file extension based on the path name
         $extension = preg_match('/\.(\w+$)/', $filePath, $matches);

         // Return an empty string if the file extension can't be found
         if (count($matches) == 0) return '';

         return $tags[$matches[1]]($filePath);
     }
}
