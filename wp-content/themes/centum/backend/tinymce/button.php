<?php
// Call WP Load
$wp_include = "../wp-load.php";
$i = 0;
while (!file_exists($wp_include) && $i++ < 10) {
    $wp_include = "../$wp_include";
} require
($wp_include);
if ( !is_user_logged_in() || !current_user_can('edit_posts') )
    wp_die(__("You are not allowed to be here","purepress"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Add Button</title>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/backend/css/tinymce.css?v=2" type="text/css" media="screen" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo site_url() ?>/wp-includes/js/tinymce/tiny_mce_popup.js?v=3211"></script>
    <script type="text/javascript" >
    tinyMCEPopup.requireLangPack();


    var ListsDialog = {
        local_ed : 'ed',
        init : function(ed) {
            ListsDialog.local_ed = ed;
            var f = document.forms[0];
            output = '';
                    // Get the selected contents as text and place it in the input
                    //f.someval.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
                    jQuery('#text').change(function(){
                        var text = $(this).val();
                        var choosedStyle = jQuery('#selector option:selected').val();
                        jQuery(".button").text(text).addClass(choosedStyle);

                    }).change();
                    jQuery('#selector').change(function(){

                        var choosedStyle = jQuery('#selector option:selected').val();
                        var choosedSize = jQuery('#size option:selected').val();
                        jQuery(".button").attr('class','button').addClass(choosedStyle).addClass(choosedSize);
                    }).change();

                },

                insert : function() {

                    var url = jQuery('#url').val();
                    var text = jQuery('#text').val();
                    var customcolor = jQuery('#customcolor').val();
                    var choosedStyle = jQuery('#selector option:selected').val();
                    var choosedSize = jQuery('#size option:selected').val();
                    var choosedicon = jQuery('#icon option:selected').val();
                    var choosediconcolor = jQuery('#iconcolor option:selected').val();
                    
                    if(choosedicon != "none") {
                        var icon = 'icon="'+choosedicon+'" iconcolor="'+choosediconcolor+'" ';
                    } else {
                        var icon = ' ';
                    }
                    if(customcolor) {
                        var pop = 'customcolor="'+ customcolor +'" ';
                    } else {
                        var pop = ' ';
                    }
                    if (text) {
                        output = '[button color="'+choosedStyle+'" size="'+choosedSize+'" url="'+url+'" '+icon+' '+pop+'] '+ text + ' [/button] ';
                    } else {
                        output = '[button color="'+choosedStyle+'" size="'+choosedSize+'" url="'+url+'" '+icon+' '+pop+'] '+ ListsDialog.local_ed.selection.getContent() + ' [/button] ';
                    }
                    // Insert the contents from the input into the document
                    tinyMCEPopup.editor.execCommand('mceInsertContent', false, output);
                    tinyMCEPopup.close();
                }
            };

            tinyMCEPopup.onInit.add(ListsDialog.init, ListsDialog);

            </script>
        </head>
        <body>

            <form onsubmit="ListsDialog.insert();return false;" action="#">
                <div id="lists" class="dialogbox">

                    <h3>Button properties</h3>
                    <p>
                        <label>URL</label>
                        <input type="text" class="tab-title" name="url" id="url"/>
                    </p>
                    <p>
                        <label>Text</label>
                        <input type="text" class="tab-title" name="text" id="text"/>
                    </p>
                    
                    <p>
                        <label>Style</label>
                        <select id="selector">
                            <option value="color">Current main color</option>
                            <option value="light">Light</option>
                            <option value="gray">Gray</option>
                        </select>
                    </p>
                    <p>
                        <label>Size</label>
                        <select id="size">
                            <option value="medium">Medium</option>
                            <option value="small">Small</option>
                            
                        </select>
                    </p>
                    <p>
                        <label>Icon</label>
                        <select id="icon">
                            <option value="none">NONE</option>
                            <option value="mini-ico-glass">glass</option>
                            <option value="mini-ico-music">music</option>
                            <option value="mini-ico-search">search</option>
                            <option value="mini-ico-envelope">envelope</option>
                            <option value="mini-ico-heart">heart</option>
                            <option value="mini-ico-star">star</option>
                            <option value="mini-ico-star-empty">star-empty</option>
                            <option value="mini-ico-user">user</option>
                            <option value="mini-ico-film">film</option>
                            <option value="mini-ico-th-large">th-large</option>
                            <option value="mini-ico-th">th</option>
                            <option value="mini-ico-th-list">th-list</option>
                            <option value="mini-ico-ok">ok</option>
                            <option value="mini-ico-remove">remove</option>
                            <option value="mini-ico-zoom-in">zoom-in</option>
                            <option value="mini-ico-zoom-out">zoom-out</option>
                            <option value="mini-ico-off">off</option>
                            <option value="mini-ico-signal">signal</option>
                            <option value="mini-ico-cog">cog</option>
                            <option value="mini-ico-trash">trash</option>
                            <option value="mini-ico-home">home</option>
                            <option value="mini-ico-file">file</option>
                            <option value="mini-ico-time">time</option>
                            <option value="mini-ico-road">road</option>
                            <option value="mini-ico-download-alt">download-alt</option>
                            <option value="mini-ico-download">download</option>
                            <option value="mini-ico-upload">upload</option>
                            <option value="mini-ico-inbox">inbox</option>
                            <option value="mini-ico-play-circle">play-circle</option>
                            <option value="mini-ico-repeat">repeat</option>
                            <option value="mini-ico-refresh">refresh</option>
                            <option value="mini-ico-list-alt">list-alt</option>
                            <option value="mini-ico-lock">lock</option>
                            <option value="mini-ico-flag">flag</option>
                            <option value="mini-ico-headphones">headphones</option>
                            <option value="mini-ico-volume-off">volume-off</option>
                            <option value="mini-ico-volume-down">volume-down</option>
                            <option value="mini-ico-volume-up">volume-up</option>
                            <option value="mini-ico-qrcode">qrcode</option>
                            <option value="mini-ico-barcode">barcode</option>
                            <option value="mini-ico-tag">tag</option>
                            <option value="mini-ico-tags">tags</option>
                            <option value="mini-ico-book">book</option>
                            <option value="mini-ico-bookmark">bookmark</option>
                            <option value="mini-ico-print">print</option>
                            <option value="mini-ico-camera">camera</option>
                            <option value="mini-ico-font">font</option>
                            <option value="mini-ico-bold">bold</option>
                            <option value="mini-ico-italic">italic</option>
                            <option value="mini-ico-text-height">text-height</option>
                            <option value="mini-ico-text-width">text-width</option>
                            <option value="mini-ico-align-left">align-left</option>
                            <option value="mini-ico-align-center">align-center</option>
                            <option value="mini-ico-align-right">align-right</option>
                            <option value="mini-ico-align-justify">align-justify</option>
                            <option value="mini-ico-list">list</option>
                            <option value="mini-ico-indent-left">indent-left</option>
                            <option value="mini-ico-indent-right">indent-right</option>
                            <option value="mini-ico-facetime-video">facetime-video</option>
                            <option value="mini-ico-picture">picture</option>
                            <option value="mini-ico-pencil">pencil</option>
                            <option value="mini-ico-map-marker">map-marker</option>
                            <option value="mini-ico-adjust">adjust</option>
                            <option value="mini-ico-tint">tint</option>
                            <option value="mini-ico-edit">edit</option>
                            <option value="mini-ico-share">share</option>
                            <option value="mini-ico-check">check</option>
                            <option value="mini-ico-move">move</option>
                            <option value="mini-ico-step-backward">step-backward</option>
                            <option value="mini-ico-fast-backward">fast-backward</option>
                            <option value="mini-ico-backward">backward</option>
                            <option value="mini-ico-play">play</option>
                            <option value="mini-ico-pause">pause</option>
                            <option value="mini-ico-stop">stop</option>
                            <option value="mini-ico-forward">forward</option>
                            <option value="mini-ico-fast-forward">fast-forward</option>
                            <option value="mini-ico-step-forward">step-forward</option>
                            <option value="mini-ico-eject">eject</option>
                            <option value="mini-ico-chevron-left">chevron-left</option>
                            <option value="mini-ico-chevron-right">chevron-right</option>
                            <option value="mini-ico-plus-sign">plus-sign</option>
                            <option value="mini-ico-minus-sign">minus-sign</option>
                            <option value="mini-ico-remove-sign">remove-sign</option>
                            <option value="mini-ico-ok-sign">ok-sign</option>
                            <option value="mini-ico-question-sign">question-sign</option>
                            <option value="mini-ico-info-sign">info-sign</option>
                            <option value="mini-ico-screenshot">screenshot</option>
                            <option value="mini-ico-remove-circle">remove-circle</option>
                            <option value="mini-ico-ok-circle">ok-circle</option>
                            <option value="mini-ico-ban-circle">ban-circle</option>
                            <option value="mini-ico-arrow-left">arrow-left</option>
                            <option value="mini-ico-arrow-right">arrow-right</option>
                            <option value="mini-ico-arrow-up">arrow-up</option>
                            <option value="mini-ico-arrow-down">arrow-down</option>
                            <option value="mini-ico-share-alt">share-alt</option>
                            <option value="mini-ico-resize-full">resize-full</option>
                            <option value="mini-ico-resize-small">resize-small</option>
                            <option value="mini-ico-plus">plus</option>
                            <option value="mini-ico-minus">minus</option>
                            <option value="mini-ico-asterisk">asterisk</option>
                            <option value="mini-ico-exclamation-sign">exclamation-sign</option>
                            <option value="mini-ico-gift">gift</option>
                            <option value="mini-ico-leaf">leaf</option>
                            <option value="mini-ico-fire">fire</option>
                            <option value="mini-ico-eye-open">eye-open</option>

                            <option value="mini-ico-eye-close">eye-close</option>
                            <option value="mini-ico-warning-sign">warning-sign</option>
                            <option value="mini-ico-plane">plane</option>
                            <option value="mini-ico-calendar">calendar</option>
                            <option value="mini-ico-random">random</option>
                            <option value="mini-ico-comment">comment</option>
                            <option value="mini-ico-magnet">magnet</option>
                            <option value="mini-ico-chevron-up">chevron-up</option>
                            <option value="mini-ico-chevron-down">chevron-down</option>
                            <option value="mini-ico-retweet">retweet</option>
                            <option value="mini-ico-shopping-cart">shopping-cart</option>
                            <option value="mini-ico-folder-close">folder-close</option>
                            <option value="mini-ico-folder-open">folder-open</option>
                            <option value="mini-ico-resize-vertical">resize-vertical</option>
                            <option value="mini-ico-resize-horizontal">resize-horizontal</option>
                            <option value="mini-ico-hdd">hdd</option>
                            <option value="mini-ico-bullhorn">bullhorn</option>
                            <option value="mini-ico-bell">bell</option>
                            <option value="mini-ico-certificate">certificate</option>
                            <option value="mini-ico-thumbs-up">thumbs-up</option>
                            <option value="mini-ico-thumbs-down">thumbs-down</option>
                            <option value="mini-ico-hand-right">hand-right</option>
                            <option value="mini-ico-hand-left">hand-left</option>
                            <option value="mini-ico-hand-up">hand-up</option>
                            <option value="mini-ico-hand-down">hand-down</option>
                            <option value="mini-ico-circle-arrow-right">circle-arrow-right</option>
                            <option value="mini-ico-circle-arrow-left">circle-arrow-left</option>
                            <option value="mini-ico-circle-arrow-up">circle-arrow-up</option>
                            <option value="mini-ico-circle-arrow-down">circle-arrow-down</option>
                            <option value="mini-ico-globe">globe</option>
                            <option value="mini-ico-wrench">wrench</option>
                            <option value="mini-ico-tasks">tasks</option>
                            <option value="mini-ico-filter">filter</option>
                            <option value="mini-ico-briefcase">briefcase</option>
                            <option value="mini-ico-fullscreen">fullscreen</option>
                        </select>
                    </p>
                    <p>
                        <label>Icon color</label>
                        <select id="iconcolor">
                            <option value="black">Black</option>
                            <option value="white">White</option>
                        </select>
                    </p>
                    <p>
                       <label>Custom color hash</label>
                        <input type="text" class="popuptitle" name="customcolor" id="customcolor"/>
                    </p>
                
                    <div id="demo">
                        <h3>Button example: </h3>
                        <a href="#" class="button medium">Button example</a>
                    </div>
                </div>

                <div class="mceActionPanel">
                    <input type="button" id="insert" name="insert" value="{#insert}" onclick="ListsDialog.insert();" />
                    <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
                </div>
            </form>

        </body>
        </html>
