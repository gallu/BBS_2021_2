<?php    // twig_test.php
//  https://dev2.m-fr.net/アカウント名/BBS/twig_test.php

require_once( __DIR__ . '/../vendor/autoload.php');
/*
$loader = new \Twig\Loader\FilesystemLoader( __DIR__ . '/../templates');
$twig = new \Twig\Environment($loader);
*/
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$loader = new FilesystemLoader( __DIR__ . '/../templates');
$twig = new Environment($loader);

echo $twig->render('twig_test.twig', ['name' => 'おいちゃん']);
