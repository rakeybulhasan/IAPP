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
	<title>Accordion creator</title>
<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/backend/css/tinymce.css" type="text/css" media="screen" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js?v=3211"></script>
	<script type="text/javascript" >
            tinyMCEPopup.requireLangPack();


        var AccordionDialog = {
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

               

                jQuery('.tabcont').each(function(index) {
                    var title = $(this).find('.tab-title').val();
                    var content = $(this).find('.tab-content').val();
                    output += ' [accordion title="'+title+'"] '+ content + ' [/accordion] ';
                });
               
		// Insert the contents from the input into the document
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, output);
		tinyMCEPopup.close();
            }
        };

tinyMCEPopup.onInit.add(AccordionDialog.init, AccordionDialog);

        </script>
</head>
<body>

<form onsubmit="AccordionDialog.insert();return false;" action="#">
    <div class="dialogbox" id="tabs">
        <div class="tabcont">
            <h3>Accordion</h3>
            <p>
                <label>Title:</label>
                <input class="tab-title" name="tab" type="text" class="text" />
            </p>
            <p>
                <label>Content:</label>
                <textarea class="tab-content" name="tab-content" class="text" ></textarea>
            </p>
           <button class="updateButton removeTab">Remove</button>
        </div>
        
    </div>
    <br/>
    <a href="#" class="button small yellow" id="newtab">Add new Accordion</a>
    <div class="mceActionPanel">
            <input type="button" id="insert" name="insert" value="{#insert}" onclick="AccordionDialog.insert();" />
            <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
    </div>
</form>

</body>
</html>
