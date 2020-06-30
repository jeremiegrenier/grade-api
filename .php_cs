<?php

use Coffreo\PhpCsFixer\Config;

$header = <<<HEADER
This file is part of grade-api project
HEADER;

$config = Config\Factory::fromRuleSet(

    // CHOOSE YOUR RULE SET HERE
    new Config\RuleSet\Php72($header)

);
$config->getFinder()->in(__DIR__.'/src');
$config->setCacheFile(__DIR__.'/.php_cs.cache');

return $config;
