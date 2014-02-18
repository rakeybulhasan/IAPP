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
        <title>Columns creator</title>
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
                    jQuery('.select-col').click(function(){

                        jQuery(".select-col").attr('class', 'select-col');
                          jQuery(this).addClass('active');

                    });

                },

                insert : function() {

                    var width = jQuery('#width option:selected').val();
                    var place = jQuery('#placement option:selected').val();

                    output = '[column width="'+width+'" place="'+place+'" ] '+ ListsDialog.local_ed.selection.getContent() + ' [/column] ';

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


                <h3>Column shortcode properties</h3>
                    <p>
                        <label>Width</label>
                        <select id="width">

                            <option value="1/3">1/3</option>
                            <option value="2/3">2/3</option>
                            <option value="full">Full width (16)</option>
                            <option value="one">One</option>
                            <option value="two">Two</option>
                            <option value="three">Three</option>
                            <option value="four">Four</option>
                            <option value="five">Five</option>
                            <option value="six">Six</option>
                            <option value="seven">Seven</option>
                            <option value="eight">Eight</option>
                            <option value="nine">Nine</option>
                            <option value="ten">Ten</option>
                            <option value="eleven">Eleven</option>
                            <option value="twelve">Twelve</option>
                            <option value="thirteen">Thirteen</option>
                            <option value="fourteen">Fourteen</option>
                            <option value="fifteen">Fifteen</option>
                            <option value="sixteen">Sixteen</option>
                        </select>
                    </p>
                    <p>
                        <label>Placement</label>
                        <select id="placement">
                            <option value="none">none</option>
                            <option value="first">first</option>
                            <option value="center">center</option>
                            <option value="last">last</option>
                        </select>
                    </p>

                    <p><strong>Info:</strong> Full width page can be divided to 16 columns grid so while creating layouts keep in mind that sum of all columns in one row must be 16</p>
                    <p>If you're using "Home Page" template first column in row should have <strong>place="none"</strong>, in any other case it should be <strong>place="first"</strong>
                <div class="mceActionPanel">
                    <input type="button" id="insert" name="insert" value="{#insert}" onclick="ListsDialog.insert();" />
                    <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
                </div>
        </form>

    </body>
</html>
