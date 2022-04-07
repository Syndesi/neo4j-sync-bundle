<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__.'/src/',
        __DIR__.'/tests/'
    ])
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
;
