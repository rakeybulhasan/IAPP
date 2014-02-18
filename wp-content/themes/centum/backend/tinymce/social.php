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
    <title>Add Social Button</title>
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
                 

                },

                insert : function() {
                    var url = jQuery('#url').val();
                    var service = jQuery('#selector option:selected').val();
                    output = '[social_icon url="'+url+'" service="'+service+'"] ';
                
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
                        <label>Color</label>
                        <select id="selector">
                            <option value="rss">RSS</option>
                            <option value="twitter">Twitter</option>
                            <option value="facebook">Facebook</option>
                            <option value="dribbble">Dribbble</option>
                            <option value="flickr">Flickr</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="lastfm">LastFM</option>
                            <option value="skype">Skype</option>
                            <option value="newsvine">Newsvine</option>
                            <option value="vimeo">Vimeo</option>
                            <option value="apple">Apple</option>
                            <option value="googleplus">Google+</option>
                            <option value="youtube">YouTube</option>
                            <option value="feedburner">FeedBurner</option>
                            <option value="digg">Digg</option>
                            <option value="wordpress">WordPress</option>
                            <option value="tumblr">Tumblr</option>
                            <option value="sharethis">ShareIt</option>
                            <option value="deviantart">DeviantArt</option>
                            <option value="blogger">Blogger</option>
                            <option value="forrst">Forrst</option>
                            <option value="mail">Mail</option>
                        </select>
                    </p>

                  
                </div>

                <div class="mceActionPanel">
                    <input type="button" id="insert" name="insert" value="{#insert}" onclick="ListsDialog.insert();" />
                    <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
                </div>
            </form>

        </body>
        </html>
