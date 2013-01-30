<?php

session_start();

include_once 'VkPhotoUpload.php';
include_once 'VkAuth.php';

$VkAuth = new VkAuth();
$VkAuth->authorize();

$VkPhotoUpload = new VkPhotoUpload();
$VkPhotoUpload->upload($VkAuth->getAccessToken());
