<?php namespace Castiron\WebpackAssets\Services;

use October\Rain\Exception\ApplicationException;

/**
 * Class ManifestLoader
 * @package Castiron\WebpackAssets\Services
 */
class ManifestLoader
{
    /**
     * @var
     */
    var $publicFolder;

    /**
     * @var
     */
    var $assetsFolder;

    /**
     * @var
     */
    var $manifestFilename;

    /**
     * @param $publicFolder
     * @param $assetsFolder
     * @param $manifestFilename
     * @return void
     */
    function __construct($publicFolder = 'www', $assetsFolder = 'assets', $manifestFilename = 'manifest.json')
    {
        $this->publicFolder = $publicFolder;
        $this->assetsFolder = $assetsFolder;
        $this->manifestFilename = $manifestFilename;
    }

    /**
     * @return string
     */
    public function assetManifestPath()
    {
        $path = [
            $this->publicFolder,
            $this->assetsFolder,
            $this->manifestFilename
        ];
        return public_path(
            implode(DIRECTORY_SEPARATOR, $path)
        );
    }

    /**
     * @throws ApplicationException
     */
    public function getManifest()
    {
        $file = $this->assetManifestPath();
        if (!is_file($file)) {
            throw new ApplicationException('Could not load webpack manifest file ' . $file);
        }
        return (array)json_decode(file_get_contents($file));
    }
}
