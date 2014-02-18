<?php
define('MANAGER_URI', get_template_directory_uri() . '/backend');
add_action('admin_init', 'sbmanager_init');

global $pp_sidebars;

if (get_option('pp_sidebars')) {
    $pp_sidebars = get_option('pp_sidebars');
} else {
    $pp_sidebars = false;
}

// admin menu
function sbmanager_admin_menu() {

    if (isset($_GET['page']) && $_GET['page'] == 'sidebarmanager') {

        if (isset($_POST['action']) && $_POST['action'] == 'save') {
            // if ( 'save' == $_REQUEST['action'] ) {
            $pp_sidebars = array();

            foreach ($_POST['name'] as $k => $v) {
//                $safename = str_replace(" ","",$v);
                $pp_sidebars[] = array(
                    'name' => $v,
                    'desc' => $_POST['desc'][$k]
                );
            }

            update_option('pp_sidebars', $pp_sidebars);
            header("Location: themes.php?page=sidebarmanager&saved=true");
        }
    }
}

// slider manager wrapper
function sbmanager_wrap() {
    global $pp_sidebars;
    ?>

    <div class="wrap" id="manager_wrap">
        <form action="" id="manager_form" method="post">
            <div id="icon-options-general" class="icon32"></div>
            <h2 ><?php _e("Sidebars manager", 'purepress'); ?></h2>
            <p><?php _e("Create and manage your sidebars, and then assign them to specific posts or pages", 'purepress'); ?>
            </p>


            <ul id="manager_form_wrap">

                <?php if (get_option('pp_sidebars')) : ?>

                    <?php foreach ($pp_sidebars as $k => $pp_sidebar) : ?>

                        <li class="sidebar">
                            <div class="widgets-holder-wrap ">
                                <div class="sidebar-name">
                                     <div class="sidebar-name-arrow"><br></div><h3>Slide: <span></span></h3>
                                </div>
                                <div class="slide-inside" >
                                    <div style="overflow: hidden">
                                        <p>
                                            <label><?php _e("Sidebar Name", 'purepress'); ?></label>
                                            <input type="text" name="name[]" id="sb_name" value="<?php echo $pp_sidebar['name'] ?>">
                                        </p>
                                        <p>
                                            <label><?php _e("Sidebar Description", 'purepress'); ?></label>
                                            <textarea name="desc[]" cols="20" rows="2" class="sb_desc"><?php echo $pp_sidebar['desc'] ?></textarea>
                                        </p>

                                    </div>
                                    <button class="remove_slide button-secondary"><?php _e("Remove This Sidebar", 'purepress'); ?></button>
                                </div>
                            </div>
                        </li>

                    <?php endforeach; ?>

                <?php else : ?>

                    <li class="sidebar">
                        <div class="widgets-holder-wrap ">
                            <div class="sidebar-name">
                                <div class="sidebar-name-arrow"><br></div><h3>Sidebar: <span></span></h3>
                            </div>
                            <div class="slide-inside" >
                                <p>
                                    <label><?php _e("Sidebar Name", 'purepress'); ?></label>

                                    <input type="text" name="name[]" id="sb_name">
                                </p>
                                <p>
                                    <label><?php _e("Sidebar Description", 'purepress'); ?></label>
                                    <textarea name="desc[]" cols="20" rows="2" class="sb_desc"></textarea>
                                </p>
                                <button class="remove_slide button-secondary"><?php _e("Remove This Sidebar", 'purepress'); ?></button>
                            </div>
                        </div>
                    </li>

                <?php endif; ?>

            </ul>

            <input type="submit" value="<?php _e("Save Changes", 'purepress'); ?>" id="manager_submit" class="button-primary">
            <input type="hidden" name="action" value="save">

        </form>

    </div>

    <?php
}

// slider manager init
function sbmanager_init() {

    if (isset($_GET['page']) && $_GET['page'] == 'sidebarmanager') {

        // scripts
        wp_enqueue_script('jquery-ui-core');

        wp_enqueue_script('jquery-appendo', get_template_directory_uri() . '/backend/js/jquery.appendo.js', false, '1.0', false);
        wp_enqueue_script('sidebar-manager', get_template_directory_uri() . '/backend/js/sbmanager.js', false, '1.0', false);
        // styles
        wp_enqueue_style('slider-manager', MANAGER_URI . '/css/sbmanager.css', false, '1.0', 'all');
    }
}
?>