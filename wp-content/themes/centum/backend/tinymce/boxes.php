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
        <title>Add Boxes</title>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/backend/css/tinymce.css" type="text/css" media="screen" />
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js?v=3211"></script>
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
                    jQuery('#selector').change(function(){

                        jQuery("#selector option:selected").each(function () {

                           var style = $(this).val();
                            jQuery("#demo div.notification").attr('class', 'notification '+style);

                          });
                    }).change();

                },

                insert : function() {

                    var choosedStyle = jQuery('#selector option:selected').val();

                    output = ' [box type='+choosedStyle+'] '+ ListsDialog.local_ed.selection.getContent() + ' [/box] ';

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
            <div class="dialogbox" id="lists">

                    <h3>Choose list style</h3>
                    <p>
                        <select id="selector">
                            <option value="notice">Notice box</option>
                            <option value="error">Error  box</option>
                            <option value="warning">Warning  box</option>
                            <option value="success">Success box</option>
                        </select>
                    </p>
                    <div id="demo">
                        <h3>Box example: </h3>
                        <div class="notification notice">
                            <p>Normal theme box.</p>

                        </div>

                    </div>
            </div>

            <div class="mceActionPanel">
                <input type="button" id="insert" name="insert" value="{#insert}" onclick="ListsDialog.insert();" />
                <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
            </div>
        </form>

    </body>
</html>
