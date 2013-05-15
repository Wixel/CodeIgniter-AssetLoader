CI-AssetLoad
============

A CodeIgniter library for loading CSS & JS assets from a manifest file supporting different environments with an optional cache busting mechanism.

Usage:
-----

1. Download and extract to your CodeIgniter libraries directory
2. Either autoload the library or explicitly load it in your controllers
3. Define your asset manifest file for your environments and optionally provide default assets for all environments
4. Replace the css link & script tags with `<?php $this->assetload->queue(); ?>` in your template head section
5. Enjoy the sanity that follows! :)

#  Methods

```php
AssetLoad::queue($cache_bust = false, $manifest_path = 'assets/', $manifest_file_name = 'assets.ini')
```

# Changing Defaults

The loader assumes all your assets are contained in child directories within the `/assets` directory in your `project root`, but you can change this 
by specifying the parent asset directory as follows:

```php
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Your title</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
	<?php $this->assetload->queue(false, '/static'); ?>
</head>
```

If you wish to enable cache-busting by automatically adding a timestamp to include paths in production or testing environments, simply change the first argument to the `queue()` method to true 
as follows:

```php
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Your title</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
	<?php $this->assetload->queue(true); ?>
</head>
```

Defining a Manifest File:
-----

Your manifest file should be called `assets.ini` and placed inside your parent assets directory: `/assets/assets.ini`

# Structure

```
; Asset loader manifest file

[defaults]
css[] = "css/reset.css"
js[]  = "js/html5shiv.min.js"

[development]
css[] = "css/application.css"
js[]  = "js/vendor/modernizr-2.6.2.min.js"
js[]  = "js/vendor/raphael-min.js"
js[]  = "js/plugins.js"
js[]  = "js/application.js"
js[]  = "js/debugger.js"

[production]
css[] = "css/compiled.css"
js[]  = "js/compiled.js"

[testing]
css[] = "css/application.css"
js[]  = "js/vendor/modernizr-2.6.2.min.js"
js[]  = "js/vendor/raphael-min.js"
js[]  = "js/plugins.js"
js[]  = "js/application.js"
```

#  TODO

- Wildcard lazy-loader
- Ability to specify link and script tag attributes like defer & async
- Split css and js loader methods to allow loading scripts near the end of the body tag