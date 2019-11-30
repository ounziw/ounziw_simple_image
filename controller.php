<?php 
namespace Concrete\Package\OunziwSimpleImage;

use BlockType;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends \Concrete\Core\Package\Package {

    protected $pkgHandle = 'ounziw_simple_image';
    protected $appVersionRequired = '5.7.5';
    protected $pkgVersion = '0.9.3';

    public function getPackageDescription() {
        return t("Users can upload and display images quickly. Helpful for those who consider c5's file manager too heavy.");
    }

    public function getPackageName() {
        return t("Simple Image Block");
    }

    public function install() {
        $pkg = parent::install();
        BlockType::installBlockType('simple_image', $pkg);
    }
}