<?php

add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );

/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
    register_setting( 'ecb_options', 'ecb_theme_options', 'theme_options_validate' );
}

/**
 * Load up the menu page
 */
function theme_options_add_page() {
    add_theme_page( __( 'Category Settings' ), __( 'Category Settings' ), 'edit_theme_options', 'site-settings', 'theme_options_do_page' );
}

/**
 * Create the options page
 */
function theme_options_do_page() {
    global $select_options, $radio_options;

    if ( ! isset( $_REQUEST['updated'] ) )
        $_REQUEST['updated'] = false;

    ?>
<div class="wrap">
    <?php screen_icon(); echo "<h2>" . get_current_theme() . __( ' Theme Options' ) . "</h2>"; ?>

    <?php if ( false !== $_REQUEST['updated'] ) : ?>
    <div class="updated fade"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
    <?php endif; ?>

    <form method="post" action="options.php">
        <?php settings_fields( 'ecb_options' ); ?>
        <?php $options = get_option( 'ecb_theme_options' ); ?>

        <table class="form-table">

            <tr valign="top"><th scope="row"><?php _e( 'Component for Home' ); ?></th>
                <td>
                    <?php
                    $selected = $options['component'];
                    wp_dropdown_categories("selected=$selected&name=ecb_theme_options[component]&hierarchical=1&hide_empty=0");
                    ?>
                    <label class="description" for="ecb_theme_options[component]"><?php _e( 'Select a category to component category for home page' ); ?></label>
                </td>
            </tr>
            <tr valign="top"><th scope="row"><?php _e( 'Home Welcome Message' ); ?></th>
                <td>
                    <?php
                    $selected = $options['welcome-message'];
                    wp_dropdown_categories("selected=$selected&name=ecb_theme_options[welcome-message]&hierarchical=1&hide_empty=0");
                    ?>
                    <label class="description" for="ecb_theme_options[welcome-message]"><?php _e( 'Select a category to show content for home welcome message' ); ?></label>
                </td>
            </tr>
           <tr valign="top"><th scope="row"><?php _e( 'Banner' ); ?></th>
                        <td>
                            <?php
                            $selected = $options['banner_gallery'];
                            wp_dropdown_pages("selected=$selected&name=ecb_theme_options[banner_gallery]&hierarchical=1");
                            ?>
                            <label class="description" for="ecb_theme_options[banner_gallery]"><?php _e( 'Select a page to show content for Banner' ); ?></label>
                        </td>
                    </tr>

            <tr valign="top"><th scope="row"><?php _e( 'Category set for Component Template' ); ?></th>
                <td>
                    <?php
                    $categories = get_categories(array('hide_empty' => 0));
                    $component_category_template = $options['component_category_template'];
                    ?>
                    <select multiple="multiple" name="ecb_theme_options[component_category_template][]" id="" size="8">
                        <?php foreach ($categories as $category){
                            $selected = (in_array($category->term_id, $component_category_template)) ? 'selected="selected"' : '';
                            ?>
                        <option value="<?php echo $category->term_id?>"<?php echo $selected?>><?php echo $category->name?></option>
                        <?php } ?>
                    </select>

                    <label class="description" for="ecb_theme_options[component_category_template]"><?php _e( 'Select categories to show content for category template' ); ?></label>
                </td>
            </tr>

            <tr valign="top"><th scope="row"><?php _e( 'Category set for TA Component Template' ); ?></th>
                <td>
                    <?php
                    $categories = get_categories(array('hide_empty' => 0));
                    $ta_component_category_template = $options['ta_component_category_template'];
                    ?>
                    <select multiple="multiple" name="ecb_theme_options[ta_component_category_template][]" id="" size="8">
                        <?php foreach ($categories as $category){
                            $selected = (in_array($category->term_id, $ta_component_category_template)) ? 'selected="selected"' : '';
                            ?>
                        <option value="<?php echo $category->term_id?>"<?php echo $selected?>><?php echo $category->name?></option>
                        <?php } ?>
                    </select>

                    <label class="description" for="ecb_theme_options[ta_component_category_template]"><?php _e( 'Select TA category to show content for category template' ); ?></label>
                </td>
            </tr>
            <tr valign="top"><th scope="row"><?php _e( 'Category sorting order' ); ?></th>
                <td>

                    <textarea id="ecb_theme_options[category_sorting]" name="ecb_theme_options[category_sorting]" rows="5" cols="36"><?php esc_attr_e( $options['category_sorting'] ); ?></textarea>

                    <label class="description" for="ecb_theme_options[category_sorting]"><?php _e( 'Category sorting order value' ); ?></label>
                </td>
            </tr>


        </table>

        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e( 'Save Options' ); ?>" />
        </p>
    </form>


</div>

<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function theme_options_validate( $input ) {
    global $select_options, $radio_options;

    $input['sometextarea'] = wp_filter_post_kses( $input['sometextarea'] );
    return $input;
}

