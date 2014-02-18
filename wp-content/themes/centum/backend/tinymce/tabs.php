<?php
// Call WP Load
$wp_include = "../wp-load.php";$i = 0;
while (!file_exists($wp_include) && $i++ < 10) {$wp_include = "../$wp_include";} require($wp_include);
if ( !is_user_logged_in() || !current_user_can('edit_posts') )
	wp_die(__("You are not allowed to be here","purepress"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Tabs creator</title>
<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/backend/css/tinymce.css" type="text/css" media="screen" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo home_url() ?>/wp-includes/js/tinymce/tiny_mce_popup.js?v=3211"></script>
	<script type="text/javascript" >
            tinyMCEPopup.requireLangPack();


        var TabsDialog = {
            init : function() {
		var f = document.forms[0];
                output = '';
		// Get the selected contents as text and place it in the input
		//f.someval.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		jQuery('#newtab').click(function(){
                    jQuery('.tabcont:first').clone().addClass('newtab').appendTo('#tabs');
                    return false;
                });

                jQuery('.removeTab').live('click', function() {
                    if(jQuery('#tabs .tabcont').size() == 1) {
                        alert('Sorry, you need at least one element');
                    }
                    else {
                        jQuery(this).parent().slideUp(300, function() {
                            jQuery(this).remove();
                        })
                    }
                    return false;
                });
            },

            insert : function() {

                output = ' [tabgroup] ';

                jQuery('.tabcont').each(function(index) {
                    var title = $(this).find('.tab-title').val();
                    var choosedicon = $(this).find('#tab-icon option:selected').val();
                    var content = $(this).find('.tab-content').val();
                    if(choosedicon != "none") {
                        var icon = 'icon="mini-'+choosedicon+'"';
                    } else {
                        var icon = ' ';
                    }
                    output += ' [tab title='+title+' '+icon+'] '+ content + ' [/tab] ';
                });
                output += ' [/tabgroup] ';
		// Insert the contents from the input into the document
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, output);
		tinyMCEPopup.close();
            }
        };

tinyMCEPopup.onInit.add(TabsDialog.init, TabsDialog);

        </script>
</head>
<body>

<form onsubmit="TabsDialog.insert();return false;" action="#">

    <div id="tabs" class="dialogbox">
        <div class="tabcont">
            <h3>Tab</h3>
            <p>
                <label>Tab title:</label>
                <input class="tab-title" name="tab" type="text" class="text" />
            </p>
            <p>
                        <label>Tab Icon</label>
                        <select id="tab-icon">
                            <option value="none">NONE</option>
                    <option value="ico-glass">glass</option>
                    <option value="ico-music">music</option>
                    <option value="ico-search">search</option>
                    <option value="ico-envelope">envelope</option>
                    <option value="ico-heart">heart</option>
                    <option value="ico-star">star</option>
                    <option value="ico-star-empty">star-empty</option>
                    <option value="ico-user">user</option>
                    <option value="ico-film">film</option>
                    <option value="ico-th-large">th-large</option>
                    <option value="ico-th">th</option>
                    <option value="ico-th-list">th-list</option>
                    <option value="ico-ok">ok</option>
                    <option value="ico-remove">remove</option>
                    <option value="ico-zoom-in">zoom-in</option>
                    <option value="ico-zoom-out">zoom-out</option>
                    <option value="ico-off">off</option>
                    <option value="ico-signal">signal</option>
                    <option value="ico-cog">cog</option>
                    <option value="ico-trash">trash</option>
                    <option value="ico-home">home</option>
                    <option value="ico-file">file</option>
                    <option value="ico-time">time</option>
                    <option value="ico-road">road</option>
                    <option value="ico-download-alt">download-alt</option>
                    <option value="ico-download">download</option>
                    <option value="ico-upload">upload</option>
                    <option value="ico-inbox">inbox</option>
                    <option value="ico-play-circle">play-circle</option>
                    <option value="ico-repeat">repeat</option>
                    <option value="ico-refresh">refresh</option>
                    <option value="ico-list-alt">list-alt</option>
                    <option value="ico-lock">lock</option>
                    <option value="ico-flag">flag</option>
                    <option value="ico-headphones">headphones</option>
                    <option value="ico-volume-off">volume-off</option>
                    <option value="ico-volume-down">volume-down</option>
                    <option value="ico-volume-up">volume-up</option>
                    <option value="ico-qrcode">qrcode</option>
                    <option value="ico-barcode">barcode</option>
                    <option value="ico-tag">tag</option>
                    <option value="ico-tags">tags</option>
                    <option value="ico-book">book</option>
                    <option value="ico-bookmark">bookmark</option>
                    <option value="ico-print">print</option>
                    <option value="ico-camera">camera</option>
                    <option value="ico-font">font</option>
                    <option value="ico-bold">bold</option>
                    <option value="ico-italic">italic</option>
                    <option value="ico-text-height">text-height</option>
                    <option value="ico-text-width">text-width</option>
                    <option value="ico-align-left">align-left</option>
                    <option value="ico-align-center">align-center</option>
                    <option value="ico-align-right">align-right</option>
                    <option value="ico-align-justify">align-justify</option>
                    <option value="ico-list">list</option>
                    <option value="ico-indent-left">indent-left</option>
                    <option value="ico-indent-right">indent-right</option>
                    <option value="ico-facetime-video">facetime-video</option>
                    <option value="ico-picture">picture</option>
                    <option value="ico-pencil">pencil</option>
                    <option value="ico-map-marker">map-marker</option>
                    <option value="ico-adjust">adjust</option>
                    <option value="ico-tint">tint</option>
                    <option value="ico-edit">edit</option>
                    <option value="ico-share">share</option>
                    <option value="ico-check">check</option>
                    <option value="ico-move">move</option>
                    <option value="ico-step-backward">step-backward</option>
                    <option value="ico-fast-backward">fast-backward</option>
                    <option value="ico-backward">backward</option>
                    <option value="ico-play">play</option>
                    <option value="ico-pause">pause</option>
                    <option value="ico-stop">stop</option>
                    <option value="ico-forward">forward</option>
                    <option value="ico-fast-forward">fast-forward</option>
                    <option value="ico-step-forward">step-forward</option>
                    <option value="ico-eject">eject</option>
                    <option value="ico-chevron-left">chevron-left</option>
                    <option value="ico-chevron-right">chevron-right</option>
                    <option value="ico-plus-sign">plus-sign</option>
                    <option value="ico-minus-sign">minus-sign</option>
                    <option value="ico-remove-sign">remove-sign</option>
                    <option value="ico-ok-sign">ok-sign</option>
                    <option value="ico-question-sign">question-sign</option>
                    <option value="ico-info-sign">info-sign</option>
                    <option value="ico-screenshot">screenshot</option>
                    <option value="ico-remove-circle">remove-circle</option>
                    <option value="ico-ok-circle">ok-circle</option>
                    <option value="ico-ban-circle">ban-circle</option>
                    <option value="ico-arrow-left">arrow-left</option>
                    <option value="ico-arrow-right">arrow-right</option>
                    <option value="ico-arrow-up">arrow-up</option>
                    <option value="ico-arrow-down">arrow-down</option>
                    <option value="ico-share-alt">share-alt</option>
                    <option value="ico-resize-full">resize-full</option>
                    <option value="ico-resize-small">resize-small</option>
                    <option value="ico-plus">plus</option>
                    <option value="ico-minus">minus</option>
                    <option value="ico-asterisk">asterisk</option>
                    <option value="ico-exclamation-sign">exclamation-sign</option>
                    <option value="ico-gift">gift</option>
                    <option value="ico-leaf">leaf</option>
                    <option value="ico-fire">fire</option>
                    <option value="ico-eye-open">eye-open</option>

                    <option value="ico-eye-close">eye-close</option>
                    <option value="ico-warning-sign">warning-sign</option>
                    <option value="ico-plane">plane</option>
                    <option value="ico-calendar">calendar</option>
                    <option value="ico-random">random</option>
                    <option value="ico-comment">comment</option>
                    <option value="ico-magnet">magnet</option>
                    <option value="ico-chevron-up">chevron-up</option>
                    <option value="ico-chevron-down">chevron-down</option>
                    <option value="ico-retweet">retweet</option>
                    <option value="ico-shopping-cart">shopping-cart</option>
                    <option value="ico-folder-close">folder-close</option>
                    <option value="ico-folder-open">folder-open</option>
                    <option value="ico-resize-vertical">resize-vertical</option>
                    <option value="ico-resize-horizontal">resize-horizontal</option>
                    <option value="ico-hdd">hdd</option>
                    <option value="ico-bullhorn">bullhorn</option>
                    <option value="ico-bell">bell</option>
                    <option value="ico-certificate">certificate</option>
                    <option value="ico-thumbs-up">thumbs-up</option>
                    <option value="ico-thumbs-down">thumbs-down</option>
                    <option value="ico-hand-right">hand-right</option>
                    <option value="ico-hand-left">hand-left</option>
                    <option value="ico-hand-up">hand-up</option>
                    <option value="ico-hand-down">hand-down</option>
                    <option value="ico-circle-arrow-right">circle-arrow-right</option>
                    <option value="ico-circle-arrow-left">circle-arrow-left</option>
                    <option value="ico-circle-arrow-up">circle-arrow-up</option>
                    <option value="ico-circle-arrow-down">circle-arrow-down</option>
                    <option value="ico-globe">globe</option>
                    <option value="ico-wrench">wrench</option>
                    <option value="ico-tasks">tasks</option>
                    <option value="ico-filter">filter</option>
                    <option value="ico-briefcase">briefcase</option>
                    <option value="ico-fullscreen">fullscreen</option>
                        </select>
                    </p>
            <p>
                <label>Tab content:</label>
                <textarea class="tab-content" name="tab-content" class="text" ></textarea>
            </p>
           <button class="updateButton removeTab">Remove Tab</button>
        </div>
        
    </div>
    <br/>
    <a href="#" class="button small yellow" id="newtab">Add new tab</a>
    <div class="mceActionPanel">
            <input type="button" id="insert" name="insert" value="{#insert}" onclick="TabsDialog.insert();" />
            <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
    </div>
</form>

</body>
</html>
