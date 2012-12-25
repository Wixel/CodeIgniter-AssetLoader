CI-AssetLoad
============

A CodeIgniter library for loading CSS & JS assets from a manifest file supporting different environments

Usage:
-----

1. Download and extract to your CodeIgniter libraries directory
2. Either autoload the library or explicitly load it in your controllers
3. Define your asset manifest file for your environments
4. Replace the css link & script tags with <?php $this->assetload->queue(); ?> in your template head section
5. Enjoy the sanity that follows! :)

#  Methods

AssetLoad::queue($cache_bust = false, $manifest_path = 'assets/')

# Changing Defaults

The loader assumes all your assets are contained in child directories within the /assets directory in your project root, but you can change this 
by specifying the parent asset directory as follows:

```php
<?php $this->assetload->queue(false, '/static'); ?>
```

If you wish to enable cache-busting by automatically adding a timestamp to include paths, simply change the first argument to the queue() call to true 
as follows:

```php
<?php $this->assetload->queue(true); ?>
```

Defining a Manifest File:
-----

Your manifest file should be called assets.ini and placed inside your parent assets directory: /assets/assets.ini