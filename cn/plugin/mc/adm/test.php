<?php
if (!defined('G5_IS_ADMIN')) {
    exit;
}
if (!mc::isInstalled()) {
    mc::install();
    $root = mc();
    $context = file_get_contents(__DIR__ . '/category-example.txt');
    $root->addMulti($context);
}

