<?php  defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
if (is_object($f)) {
        $image = Core::make('html/image', array($f));
        $tag = $image->getTag();
    
    $tag->addClass('ccm-image-block img-responsive bID-'.$bID);

    print $tag;

} else if ($c->isEditMode()) { ?>

    <div class="ccm-edit-mode-disabled-item"><?php  echo t('Empty Image Block.')?></div>

<?php  } ?>
