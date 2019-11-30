<?php 

namespace Concrete\Package\OunziwSimpleImage\Block\SimpleImage;
// fork from /concrete/blocks/image

use Core;
use Database;
use Loader;
use File;
use FileImporter;
use FilePermissions;
use ValidationService;
use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    protected $btInterfaceWidth = 300;
    protected $btInterfaceHeight = 300;
    protected $btTable = 'btOunziwSimpleImage';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btWrapperClass = 'ccm-ui';
    protected $btExportFileColumns = array('fID');
    protected $btFeatures = array(
        'image',
    );
    protected $btDefaultSet = 'basic';

    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     */
    public function getBlockTypeDescription()
    {
        return t("Simply adds an image on your site.");
    }

    public function getBlockTypeName()
    {
        return t("Simple Image");
    }

    public function add() {
        $this->edit();
    }
    public function edit() {
        $token = Core::make('token')->generate('ounziw_simple_image');
        $this->set('token',$token);
    }
    public function view()
    {
        $f = File::getByID($this->fID);
        if (!is_object($f) || !$f->getFileID()) {
            return false;
        }

        $this->set('f', $f);
    }

    // This comes from core's image block.
    public function getImageFeatureDetailFileObject()
    {
        // i don't know why this->fID isn't sticky in some cases, leading us to query
        // every damn time
        $db = Database::connection();
        $fID = $db->fetchColumn('select fID from btContentImage where bID = ?', array($this->bID), 0);
        if ($fID) {
            $f = File::getByID($fID);
            if (is_object($f) && !$f->isError()) {
                return $f;
            }
        }
    }

    public function getFileID()
    {
        return $this->fID;
    }


    public function getFileObject()
    {
        return File::getByID($this->fID);
    }

    public function validate($args)
    {
        $e = Core::make('helper/validation/error');
        if ($args['fID']) {
            $fp = FilePermissions::getGlobal();
            if ($fp->canAddFiles()) {
                $fh = Loader::helper('validation/file');
                if (!is_uploaded_file($_FILES['simpleimage']['tmp_name']) || !$fh->image($_FILES['simpleimage']['tmp_name'])) {
                    $e->add(t('You must upload a valid image.'));
                }
            } else {
            $e->add(t('You do not have access to add files.'));
            }
        }
        return $e;
    }

    public function save($args)
    {
        $token = Core::make("token");
        if ($token->validate('ounziw_simple_image', $args['ounziw_simple_image'])) {

            $args['fID'] = ($args['fID'] != '') ? $args['fID'] : 0;

            $fh = Loader::helper('validation/file');
            if (isset($_FILES['simpleimage']) && is_uploaded_file($_FILES['simpleimage']['tmp_name']) && $fh->image($_FILES['simpleimage']['tmp_name']) ) {
                $fi = new FileImporter();
                $resp = $fi->import($_FILES['simpleimage']['tmp_name'], basename($_FILES['simpleimage']['name']));
                if (!($resp instanceof Version)) {
                    switch ($resp) {
                        case FileImporter::E_FILE_INVALID_EXTENSION:
                            $errors['fileupload'] = t('Invalid file extension.');
                            break;
                        case FileImporter::E_FILE_INVALID:
                            $errors['fileupload'] = t('Invalid file.');
                            break;
                    }
                }
                $args['fID'] = $resp->getFileID();
            }
            $page = \Page::getCurrentPage();
            $id = $page->getCollectionID();
            $p = \Page::getByID($id);
            if ($args['fID']) {
                $p->setAttribute('portalimgid', $args['fID']);
            }
            parent::save($args);
        } else {
            echo t('Session Expired. Please Try again.');
        }
    }
}
