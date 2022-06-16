<?php
/* 
Plugin Name: Northeastern Global Elements
Plugin URI: https://northeastern.netlify.app/pattern-library/page-chrome/global-elements/
Description: Inserts the Northeastern University global header and footer. Requires wp_body_open() under the body tag.
Author: Northeastern University ITS Web Solutions
Author URI: https://its.northeastern.edu
Version: 1.1.0
*/ 

/** 
 * Include global elements CSS, kernl UI and javascript from CDN
 */
add_action('wp_head', function() {

    echo '
        <link rel="stylesheet" href="https://global-packages.cdn.northeastern.edu/global-elements/dist/css/index.css">
        <script src="https://global-packages.cdn.northeastern.edu/global-elements/dist/js/index.umd.js"></script>
        <script src="https://global-packages.cdn.northeastern.edu/kernl-ui/dist/js/index.umd.js" defer></script>
        ';

});

/** 
 * Include the global NU header
 * 
 * NOTE: There must be a wp_body_open() statement under the <body> tag, 
 * most likely in header.php of the theme. 
 */
add_action('wp_body_open', function() {

    echo '<div
            x-data="NUGlobalElements.header({
                wordmark: true
            })"
            x-init="init()"
            style="height: 48px; background-color: black"
        ></div>';

}, 10);

/** 
 * Include the global NU footer
 */
add_action('wp_footer', function() {

    echo '<div x-data="NUGlobalElements.footer()" x-init="init()"></div>';

});

