<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

$file = __DIR__.'/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies using Composer to run the test suite.');
}
$autoload = require_once $file;

AnnotationRegistry::registerLoader(function ($class) use ($autoload) {
    $autoload->loadClass($class);

    return class_exists($class, false);
});

// Test Setup: remove files in the build/ directory
if (is_dir($buildDir = __DIR__.'/../build')) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($buildDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        $fileinfo->isDir() ? rmdir($fileinfo->getRealPath()) : unlink($fileinfo->getRealPath());
    }
}

include __DIR__.'/Fixtures/App/AppKernel.php';

$application = new Application(new AppKernel('test', true));
$application->setAutoExit(false);

// Create database
$input = new ArrayInput(array('command' => 'doctrine:database:create'));
$application->run($input, new NullOutput());
// Create database schema
$input = new ArrayInput(array('command' => 'doctrine:schema:create'));
$application->run($input, new NullOutput());
// Load fixtures of the AppTestBundle
$input = new ArrayInput(array('command' => 'doctrine:fixtures:load', '--no-interaction' => true, '--append' => true));
$application->run($input, new NullOutput());

unset($application, $input);
