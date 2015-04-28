<?php
ini_set("display_errors", 0);
if($_GET['url']) {
$imageUrl = $_GET['url'];
$file_extension = substr($imageUrl, (strrpos($imageUrl, '.') + 1));
switch( $file_extension ) {
    case "gif": $type="image/gif"; break;
    case "png": $type="image/png"; break;
    case "jpeg":
    case "jpg": $type="image/jpeg"; break;
    default:
}
if(!empty($type)) {
$imgData = file_get_contents('http://media.jsonline.com' . urldecode($imageUrl));
header('Content-type: ' . $type);
echo $imgData;
}
}