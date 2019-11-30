<?php   
defined('C5_EXECUTE') or die("Access Denied.");
$fp = FilePermissions::getGlobal();
if (!$fp->canAddFiles()) {
    echo t('You do not have access to add files.');
    exit();
}
$bf = null;

$imgdefault = \Core::getApplicationURL() . '/concrete/images/testbg.png';
$imgurl = $imgdefault;
$imgoption = array('style'=> 'height:250px;', 'accept'=> "image/*");
$disabledflag = ' style="display:none"';
if ($controller->getFileID() > 0) { 
	$bf = $controller->getFileObject();
    if(is_object($bf)) {
        $img = $bf->getApprovedVersion();
        $imgurl = $img->getURL();
        $imgoption['class'] = 'hidden';
        $disabledflag = '';
    }

}
?>
<div id="drag-drop-area">
<fieldset>
    <div class="form-group">
            <img id="thumbnail<?php  echo $bID;?>" src="<?php  echo $imgurl ;?>" width="200" height="200" <?php  echo $disabledflag;?>>
            <a class="btn btn-primary" id="simpleimagedelete" <?php  echo $disabledflag;?>><?php  echo t('Clear Image');?></a>
            <?php  echo $form->file('simpleimage',$imgoption); ?>
    </div>
    <div class="form-group">
        <?php  echo $form->text('fID', $fID, array('class'=>'hidden')); ?>
    </div>

    <?php  echo $form->hidden('ounziw_simple_image', $token); ?>
</fieldset>
</div>
<script>
    $("#simpleimage").change(function(){
        if (this.files.length > 0) {
            var file = this.files[0];
            if (file.type.match('image.*')) {
                // https://developer.mozilla.org/ja/docs/Web/API/FileReader/readAsDataURL
                var reader = new FileReader();
                reader.readAsDataURL(file);

                reader.onload = function() {
                    $("#thumbnail<?php  echo $bID;?>").attr("src", reader.result ).show();
                }
                $("#simpleimage").addClass('hidden');
                $("#simpleimagedelete").show();
            } else {
                alert("<?php  echo t('You must upload a valid image.');?>");
                $("#simpleimage").removeClass('hidden').val( null );
            }
        }
    });

    $("#simpleimagedelete").click(function(){
            $("#thumbnail<?php  echo $bID;?>").attr("src", '<?php  echo $imgdefault;?>' ).hide();
            $("#fID").val(0);
            $(this).hide();
            $("#simpleimage").removeClass('hidden').val( null );
    });
</script>