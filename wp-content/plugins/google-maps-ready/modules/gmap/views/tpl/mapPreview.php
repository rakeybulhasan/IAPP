<?php
if(empty($this->currentMap)){
    echo langGmp::_('Map not found');
    return;
}

 ?>
<?php
    $width=$this->currentMap['html_options']['width'];
    $height=$this->currentMap['html_options']['height'];
    $classname=$this->currentMap['html_options']['classname'];
    $align = $this->currentMap['html_options']['align'];
    $mapId = "ready_google_map_".$this->currentMap['id'];
    $border = ((int)$this->currentMap['html_options']['border_width'])."px solid ".$this->currentMap['html_options']['border_color'];
   $margin = $this->currentMap['html_options']["margin"];
    $ln = $this->currentMap['params']['language'];
    if($this->currentMap['params']['map_display_mode']=="popup"){
        $className="display_as_popup";
    }else{
        $className="";
    }
?>

    <style type='text/css'>

        #<?php echo $mapId;?>{
            width:<?php echo $width;?>px;
            height:<?php echo $height;?>px;
            float:<?php echo $align;  ?>;
            border:<?php echo $border;?>;
            margin:<?php echo ((int)$margin)."px";?>;
        }
        #gmapControlsNum_<?php echo $this->currentMap['id'];?>{
           width:<?php echo $width;?>px;
        }
		.gmpMarkerInfoWindow{
			width:<?php echo $this->indoWindowSize['width'];?>px;
			height:<?php echo $this->indoWindowSize['height'];?>px;
		}
   </style>
<div class='gmp_map_opts'>
    <?php
       if($this->currentMap['params']['map_display_mode']=="popup"){
           ?>
            <div class='map-preview-iumg-container'>
                <img src='<?php echo GMP_IMG_PATH."gmap_preview.png" ?>' id="show_map_icon" data_val="<?php echo $this->currentMap['id']; ?>" class='map_num_<?php echo $this->currentMap['id']; ?>' title="Click to preview map">
            </div>
           <?php
        }
    ?>
    <div class='map_container <?php echo $className;?>' id='map_container_<?php echo $this->currentMap['id']; ?>' >
        <?php
         if($this->currentMap['params']['map_display_mode']=="popup"){
             ?>
              <a class='button  close_button' onclick="closePopup()">X</a>
             <?php
         }
        ?>
       
     <div class='gmp_MapPreview <?php echo $classname;?>' id='<?php echo $mapId ;?>'     >
    
        
    </div>
    <?php
        dispatcherGmp::doAction("addMapBottomControls",$this->currentMap['id']);
    ?>
    </div>
</div>
