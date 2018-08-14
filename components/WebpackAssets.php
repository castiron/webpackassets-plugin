<?php namespace Castiron\WebpackAssets\Components;

use Castiron\WebpackAssets\Services\ManifestLoader;
use Cms\Classes\ComponentBase;
use Cms\Classes\Theme;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use October\Rain\Exception\ApplicationException;

/**
 * Class WebpackAssets
 * @package Castiron\WebpackAssets\Components
 */
class WebpackAssets extends ComponentBase
{
    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name' => 'Webpack assets',
            'description' => 'Render Javascript/CSS includes for webpack assets'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {

        return [
            'publicFolder' => [
                'title' => 'Public folder',
                'description' => 'Example: "www". the public folder (if you are using one). E.g. "public," or "www." See https://octobercms.com/docs/setup/configuration#public-folder',
                'type' => 'string',
                'default' => 'www',
            ],
            'assetsFolder' => [
                'title' => 'Assets folder',
                'description' => 'Example: "assets". The assets folder name where your resources are written by webpack',
                'type' => 'string',
                'default' => 'assets',
            ],
            'manifestFilename' => [
                'title' => 'Manifest Filename',
                'description' => 'Name of manifest filename output by Webpack (default: "manifest.json")',
                'type' => 'string',
                'default' => 'manifest.json',
            ]
        ];
    }

    /**
     * @param string $fileName The name of the json file that was written by webpack
     * @return string
     * @throws ApplicationException
     */
    public function tag($fileName)
    {
        if(!$this->appEnvDev()) {
            $cachedResult = Cache::get($this->getCacheKey($fileName));
            if($cachedResult) return $cachedResult;
        }

        $file = $this->getFile($fileName);

        if(!$this->appEnvDev()) {
            Cache::forever($this->getCacheKey($fileName), $file);
        }

        return $file;
    }

    /**
     * @return bool
     */
    protected function appEnvDev()
    {
        return env('APP_ENV') === 'dev';
    }

    /**
     * @return string
     */
    protected function assetsFolder()
    {
        return rtrim($this->property('assetsFolder'), DIRECTORY_SEPARATOR);
    }

    /**
     * @return string
     */
    protected function publicFolder()
    {
        return rtrim($this->property('publicFolder'), DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $fileName
     * @return string
     * @throws ApplicationException
     */
    protected function getFile($fileName)
    {
        /** Bail if an falsey value is passed as a filename */
        if (!$fileName) {
            throw new ApplicationException('Empty filename mapping requested from asset manifest');
        }

        // Replace with call to loader class, and pass public and assets folder
        $manifest = (new ManifestLoader(
            $this->publicFolder(),
            $this->assetsFolder(),
            $this->property('manifestFilename')
        ))->getManifest();

        /** Bail if we couldn't load the manifest */
        if (count($manifest) == 0) {
            throw new ApplicationException('Manifest file could not be loaded, or it contained no files: ' .
                $fileName
            );
        }

        /** Return an empty string if the file isn't declared in the manifest */
        if (!array_key_exists($fileName, $manifest)) {
            return '';
        }

        return $this->fileTag($manifest[$fileName]);
    }

    /**
     * @param $fileName
     * @return string
     */
    protected function getCacheKey($fileName)
    {
        return '::webpackassets:tag:'.$this->publicFolder().':'.$this->assetsFolder().':'.$this->property('manifestFilename').':'.$fileName;
    }

    /**
     * @param string $filePath
     * @return string
     */
    protected function fileTag($filePath)
    {
        // Figure out the file extension based on the path name
        preg_match('/\.(\w+$)/', $filePath, $matches);

        switch (@$matches[1]) {
            case 'css':
                return $this->getCssTag($filePath);
                break;
            case 'js':
                return $this->getJsTag($filePath);
                break;
        }
        return '';
    }

    /**
     * @param $path
     * @return string
     */
    protected function getCssTag($path)
    {
        return '<link rel="stylesheet" type="text/css" href="' . $path . '">';
    }

    /**
     * @param $path
     * @return string
     */
    protected function getJsTag($path)
    {
        return '<script type="text/javascript" src="' . $path . '"></script>';
    }
}
