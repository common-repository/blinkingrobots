<?php

namespace BlinkingRobots;

$title = isset($title) ? $title : '';
?>

<div class="example-component js-example-component aload-bg">
    <?php
    DeferCSS::enqueue('example-component');
    DeferJS::enqueue('example-component');

    if ($title) {
        echo esc_html($title);
    } ?>
</div>
