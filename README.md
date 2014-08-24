yui-compressor-php-wrapper
==========================

A Simple PHP wrapper for yahoo's Yui Compressor

I started this project because I couldn't find a good PHP wrapper for the YUI-Compressor which was also easy usable with composer.
I decided to share this project with others because I also wat to give something back to the PHP community. Feel free to use this project for whatever you like. If you strungle with a problem please create an issue. Of course I'm not perfect just like this project so if you've got improvements; share them with us!

I Started to built this project with [gpbmike's PHP-YUI-Compressor project][1], and improved it the way I thought was the best.

##Usage
1. Throw the "yui-compressor-php-wrapper" folder in the "vendor" folder of your project
2. require the project in your project's composer.json file.
```json
{
    "repositories": [
      {
        "type": "vcs",
        "url": "https://github.com/dstollie/yui-compressor-php-wrapper"
      }
    ],
    "require": {
      "dstollie/yui-compressor-php-wrapper": "dev-master"
    }
}
```
3. Start coding
```php
try {
    $compressor = new Compressor('path/to/yuicompressor.jar');
    $compressor->addFile("test.js");
    $compressor->setOption('outfile', 'outfile.js');
    $compressor->addString("var x = 1 + 1;");
    echo $compressor->compress();
} catch (CompressorException $e) {
    echo $e->getMessage();
}
```
##Usage
The help you a bit, here are some examples. These examples are also viewable in the "tests/test.php" file.
###The long way
```php
try {
    $compressor = new Compressor('path/to/yuicompressor.jar');
    $compressor->addFile("test.js");
    $compressor->setOption('outfile', 'outfile.js');
    $compressor->addString("var x = 1 + 1;");
    echo $compressor->compress();
} catch (CompressorException $e) {
    echo $e->getMessage();
}
```
###The short way
```php
try {
    $compressor = new Compressor('path/to/yuicompressor.jar');
    echo $compressor->compress('test.js', 'out.js');
} catch (CompressorException $e) {
    echo $e->getMessage();
}
```
###The css short way
```php
try {
    $compressor = new Compressor('path/to/yuicompressor.jar');
    $compressor->setOption('type', 'css');
    $result = $compressor->compress('test.css', 'out.css');
    if($result === true) {
        echo "success";
    } else {
        echo $result;
    }
} catch (CompressorException $e) {
    echo $e->getMessage();
}
```
###The css short way without an output file
```php
try {
    $compressor = new Compressor('path/to/yuicompressor.jar');
    $compressor->setOption('type', 'css');
    $result = $compressor->compress('test.css');
    if($result) {
        echo "raw content: " . $compressor->getCompressionOutput();
    } else {
        echo "error: " . $compressor->getCompressionOutput();
    }
} catch (CompressorException $e) {
    echo $e->getMessage();
}
```

[1]: https://github.com/gpbmike/PHP-YUI-Compressor
