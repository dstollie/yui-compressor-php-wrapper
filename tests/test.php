<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use dstollie\YuiCompressor\Compressor;
use dstollie\YuiCompressor\CompressorException;

/*
 * The long way
 *
 */
try {
    $compressor = new Compressor('c:/yuicompressor/yuicompressor.jar');
    $compressor->addFile("test.js");
    $compressor->setOption('outfile', 'outfile.js');
    $compressor->addString("var x = 1 + 1;");
    echo $compressor->compress();
} catch (CompressorException $e) {
    echo $e->getMessage();
}

/*
 * The short way
 *
try {
    $compressor = new Compressor('c:/yuicompressor/yuicompressor.jar');
    echo $compressor->compress('test.js', 'out.js');
} catch (CompressorException $e) {
    echo $e->getMessage();
}
*/

/*
 * The css short way
 *
try {
    $compressor = new Compressor('c:/yuicompressor/yuicompressor.jar');
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
*/

/*
 * The css short way without an output file
 *
try {
    $compressor = new Compressor('c:/yuicompressor/yuicompressor.jar');
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
*/