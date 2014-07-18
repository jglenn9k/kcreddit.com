<?php
/**
 * Start admin customization
 *
 * @package WordPress
 * @subpackage Constructor
 */
// only for administrator
if (CONSTRUCTOR_DEBUG || isset($_REQUEST['debug'])) {
    require_once CONSTRUCTOR_DIRECTORY .'/libs/debug.php';
}

// init modules for admin pages
// you can disable any
$constructor_modules = array(
    __('Themes',  'constructor') => 'themes',
    __('Layout',  'constructor') => 'layout',
    __('Templates', 'constructor') => 'templates',
    __('Header',  'constructor') => 'header',
    __('Content', 'constructor') => 'content',
    __('Comments','constructor') => 'comments',
    __('Footer',  'constructor') => 'footer',
    __('Fonts',   'constructor') => 'fonts',
    __('Colors',  'constructor') => 'colors',
    __('Design',  'constructor') => 'design',
    __('CSS',     'constructor') => 'css',
    __('Images',  'constructor') => 'images',
    __('Slideshow', 'constructor') => 'slideshow',
    __('Save',    'constructor') => 'save',
    __('Clean',   'constructor') => 'clean',
    __('Help',    'constructor') => 'help'
);

require_once CONSTRUCTOR_DIRECTORY .'/libs/Constructor/Admin.php';

$admin = new Constructor_Admin();
$admin -> init($constructor_modules);
 
