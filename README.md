## Webpack Assets in October CMS

This plugin for OctoberCMS works in tandem with the node package `webpack-assets-manifest`
(https://github.com/webdeveric/webpack-assets-manifest) to include CSS, JS, and font assets
 in your site based on a JSON manifest file written to your assets directory. This will allow you to use hashed file
 names in your built files, and let October pick up the paths effortlessly.

 In non-development environments (when the APP_ENV environment variable doesn't equal "dev") these paths get cached
 so the JSON file isn't read on every request. This cache should be cleared as a part of the deploy process by running
 `php artisan cache:clear`.

### Installation

```
composer require castiron/webpackassets-plugin
```

### Quick start

This plugin provides a component called `webpackAssets`. Include the component in your view and then use the following
 to include the js/css/fonts (WOFF2 only) in your template (e.g. in a partial, layout, etc.):

```html
[webpackAssets]
==

<!-- include font <link rel="preload"> tags: -->
{{ webpackAssets.tag('unhashed_filename.woff2') | raw }}

<!-- include css <link> tags: -->
{{ webpackAssets.tag('unhashed_filename.css') | raw }}

```

```html
[webpackAssets]
==

<!-- include js <script> tags: -->
{{ webpackAssets.tag('unhashed_filename.js') | raw }}
{{ webpackAssets.tag('webpack-dev-server.js') | raw }}
```

Keep in mind that there’s a performance trade-off to preloading too many font assets,
so limit your use to only the highest-priority font assets. For the same reason, the plugin will
only create tags for the WOFF2 font format. (See [“The Critical Request”](https://calibreapp.com/blog/critical-request/) and [“The Web Fonts: Preloaded”](https://www.zachleat.com/web/preload/) for more information.)

### Component options

`publicFolder` (default: "www")


If you are [using a public folder in
 OctoberCMS](https://octobercms.com/docs/setup/configuration#public-folder) (you should be!), specify it here. E.g.
 "www" or "public"

```
[webpackAssets]
publicFolder = public
```

`assetsFolder` (default: "assets")

The path to the folder, relative to your public folder, to which webpack is writing your assets. This
 corresponds to `output.path` from your webpack config.

```
[webpackAssets]
assetsFolder = assets
```

`manifestFilename` (default: "manifest.json", must be relative to assetsFolder)

```
[webpackAssets]
manifestFilename = files.json
```
