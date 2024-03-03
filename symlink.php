<?php
    $target_folder = $_SERVER['DOCUMENT_ROOT'].'/storage/app/public';
    $link_folder = $_SERVER['DOCUMENT_ROOT'].'/storage';

    symlink($target_folder, $link_folder);

    echo 'Symlink completed';
?>