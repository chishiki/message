<?php

    foreach (glob($_SERVER['DOCUMENT_ROOT'] . '/satellites/message/php/model/*.php') AS $models) { require($models); }
    foreach (glob($_SERVER['DOCUMENT_ROOT'] . '/satellites/message/php/view/*.php') AS $views) { require($views); }
    foreach (glob($_SERVER['DOCUMENT_ROOT'] . '/satellites/message/php/controller/*.php') AS $controllers) { require($controllers); }

?>