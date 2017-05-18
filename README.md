## Webpack Assets in October CMS

This plugin for OctoberCMS works in tandem with the node package `php-manifest-webpack-plugin` to include CSS and JS
 in your site based on a PHP manifest file written to your assets directory. This will allow you to use hashed file
 names in your built files, and let October pick up the paths effortlessly.
 
### Installation

```
composer require castiron/webpackassets-plugin
```

### Quick start

This plugin provides a component called `webpackAssets`. Include the component in your view and then use the following
 to include the js/css in your template (e.g. in a partial, layout, etc.):
 
```html
[webpackAssets]
publicFolder = www
==

<!-- include css <link> tags: -->
{{ webpackAssets.css }}

```

```html
[webpackAssets]
publicFolder = www
==

<!-- include js <script> tags: -->
{{ webpackAssets.js }}

```

#### Component method parameters

For either of the above, you can specify the specific manifest file name, like:

```html
{{ webpackAssets.css('my-manifest-file-name', 'MyManifestClassName') }}
```

The parameter options are provided in case you had to generally control either the manifest file name used, 
 or the name of the PHP class written to that file, when configuring `php-manifest-webpack-plugin`. However, the 
 defaults here are the same as those in the node module for convenience.

### Component options
 
`publicFolder` (default: "")

If you are [using a public folder in 
 OctoberCMS](https://octobercms.com/docs/setup/configuration#public-folder) (you should be!), specify it here. E.g.
 "www" or "public"

```
[webpackAssets]
publicFolder = public
```

Leave this blank if you're not using a public folder.

---

`assetsFolder` (default: "assets")

The path to the folder, relative to your public folder, to which webpack is writing your assets. This
 corresponds to `output.path` from your webpack config.

```
[webpackAssets]
assetsFolder = assets
```
