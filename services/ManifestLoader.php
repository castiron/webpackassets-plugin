<?php namespace Castiron\WebpackAssets\Services;

/**
 * Class ManifestLoader
 * @package Castiron\WebpackAssets\Services
 */

class ManifestLoader {
    /**
     * @param $publicFolder, $assetFolder
     * @return void
     */
    function __construct($publicFolder, $assetsFolder) {
        $this->publicFolder = $publicFolder;
        $this->assetsFolder = $assetsFolder;
    }

    /**
     * @param $manifestFilename
     * @return string
     */
    public function assetManifestPath($manifestFilename) {
        $path = [
            $this->publicFolder,
            $this->assetsFolder,
            $manifestFilename . '.php',
        ];
        return public_path(
            implode(DIRECTORY_SEPARATOR, $path)
        );
    }

    /**
     * @param $manifestFilename
     * @throws ApplicationException
     */
    public function load($manifestFilename = 'assets-manifest') {
        $file = $this->assetManifestPath($manifestFilename);
        if (!is_file($file)) {
            throw new ApplicationException('Could not load webpack-php manifest file ' . $file);
        }
        require_once($file);
    }
}
