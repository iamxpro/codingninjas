<?php
// 1. Подключить к теме twentyseventeen woocomerce.
add_action('after_setup_theme', 'woocommerce_support');

function woocommerce_support() {
    add_theme_support('woocommerce');
}

// 2. Добавить новый post type "фильмы" с опциями: заголовок, подзаголовок, контент, картинка, категория.
if (!function_exists('genres_taxonomy')) :

    function genres_taxonomy() {
        $labels = array(
            'name' => __('Genres'),
            'singular_name' => __('Genre'),
            'search_items' => __('Search Genre'),
            'all_items' => __('All Genres'),
            'parent_item' => __('Parent Genre:'),
            'edit_item' => __('Edit Genre:'),
            'update_item' => __('Update Genre'),
            'add_new_item' => __('Add New Genre'),
            'new_item_name' => __('New Genre name'),
            'menu_name' => __('Genres'),
            'view_item' => __('View Genres')
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'query_var' => true
        );

        register_taxonomy('genres', 'films', $args);
    }

endif;

add_action('init', 'genres_taxonomy');

if (!function_exists('films_tag_taxonomy')) :

    function films_tag_taxonomy() {
        $labels = array(
            'name' => __('Tags'),
            'singular_name' => __('Tag'),
            'search_items' => __('Search Tags'),
            'popular_items' => __('Featured Tags'),
            'parent_item' => null,
            'parent_item_colon' => null,
            'all_items' => __('All Tags'),
            'edit_item' => __('Edit Tag:'),
            'update_item' => __('Update Tag'),
            'add_new_item' => __('Add New Tag'),
            'new_item_name' => __('New Tag Name'),
            'menu_name' => __('Tags'),
            'view_item' => __('View Tags')
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => false,
            'query_var' => true
        );

        register_taxonomy('tag_films', 'films', $args);
    }

endif;

add_action('init', 'films_tag_taxonomy');

if (!function_exists('register_films')) :

    function register_films() {
        $labels = array(
            'name' => __('Films'),
            'singular_name' => __('Film'),
            'add_new' => __('Add New Film'),
            'add_new_item' => __('Add New Film'),
            'edit_item' => __('Edit Film'),
            'new item' => __('New Film'),
            'all_items' => __('All Films'),
            'view_item' => __('To see a Film'),
            'search_items' => __('Search Film'),
            'not_found' => __('No Films found'),
            'not_found_in_trash' => __('There are no Films in the Trash.'),
            'menu_name' => __('Films')
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'taxonomies' => array('genres', 'tag_films'),
            'rewrite' => array('slug' => 'films'),
            'hierarchical' => false,
            'has_archive' => true,
            'supports' => array(
                'title',
                'editor',
                'thumbnail',
                'excerpt',
            ),
            'menu_icon' => 'dashicons-format-video',
            'menu_position' => 5,
        );

        register_post_type('films', $args);
    }

endif;

add_action('init', 'register_films');

// 3. Добавить возможность покупки этих фильмов через woocomerce.
class WCCPT_Product_Data_Store_CPT extends WC_Product_Data_Store_CPT {

    /**
     * Method to read a product from the database.
     * @param WC_Product
     */
    public function read(&$product) {

        $product->set_defaults();

        if (!$product->get_id() || !( $post_object = get_post($product->get_id()) ) || !in_array($post_object->post_type, array('films', 'product'))) {
            throw new Exception(__('Invalid product.', 'woocommerce'));
        }

        $id = $product->get_id();

        $product->set_props(array(
            'name' => $post_object->post_title,
            'slug' => $post_object->post_name,
            'date_created' => 0 < $post_object->post_date_gmt ? wc_string_to_timestamp($post_object->post_date_gmt) : null,
            'date_modified' => 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp($post_object->post_modified_gmt) : null,
            'status' => $post_object->post_status,
            'description' => $post_object->post_content,
            'short_description' => $post_object->post_excerpt,
            'parent_id' => $post_object->post_parent,
            'menu_order' => $post_object->menu_order,
            'reviews_allowed' => 'open' === $post_object->comment_status,
        ));

        $this->read_attributes($product);
        $this->read_downloads($product);
        $this->read_visibility($product);
        $this->read_product_data($product);
        $this->read_extra_data($product);
        $product->set_object_read(true);
    }

    /**
     * Get the product type based on product ID.
     *
     * @since 3.0.0
     * @param int $product_id
     * @return bool|string
     */
    public function get_product_type($product_id) {
        $post_type = get_post_type($product_id);
        if ('product_variation' === $post_type) {
            return 'variation';
        } elseif (in_array($post_type, array('films', 'product'))) {
            $terms = get_the_terms($product_id, 'product_type');
            return !empty($terms) ? sanitize_title(current($terms)->name) : 'simple';
        } else {
            return false;
        }
    }

}

add_filter('woocommerce_data_stores', 'woocommerce_data_stores');

function woocommerce_data_stores($stores) {
    $stores['product'] = 'WCCPT_Product_Data_Store_CPT';
    return $stores;
}

add_filter('woocommerce_get_price', 'reigel_woocommerce_get_price', 20, 2);

function reigel_woocommerce_get_price($price, $post) {
    if ($post->post->post_type === 'films')
        $price = get_post_meta($post->id, "price", true);
    return $price;
}

add_filter('the_content', 'rei_add_to_cart_button', 20, 1);

function rei_add_to_cart_button($content) {
    global $post;
    if ($post->post_type !== 'films') {
        return $content;
    }
    ob_start();
    ?>
    <form action="" method="post">
        <input name="add-to-cart" type="hidden" value="<?php echo $post->ID ?>" />
        <input name="quantity" type="number" value="1" min="1"  />
        <input name="submit" type="submit" value="Add to cart" />
    </form>
    <?php
    return $content . ob_get_clean();
}

function films_meta_box_markup($object) {
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");
    ?>
    <div>
        <label for="meta-box-film">Price: <br>
            <input name="meta-box-film" type="text" value="<?php echo get_post_meta($object->ID, "price", true); ?>"></label>
    </div>
    <?php
}

function add_film_meta_box() {
    add_meta_box("film-meta-box", "The cost of the film", "films_meta_box_markup", "films", "side", "high", null);
}

add_action("add_meta_boxes", "add_film_meta_box");

function save_film_meta_box($post_id, $post, $update) {
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    if (!current_user_can("edit_post", $post_id))
        return $post_id;

    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    $slug = "films";
    if ($slug != $post->post_type)
        return $post_id;

    $meta_box_film_value = "";

    if (isset($_POST["meta-box-film"])) {
        $meta_box_film_value = $_POST["meta-box-film"];
    }
    update_post_meta($post_id, "price", $meta_box_film_value);
}

add_action("save_post", "save_film_meta_box", 10, 3);

// 4. Разрешить регистрацию пользователей.
/*
 *  https://clip2net.com/s/3T0uXea  
 *  +
 *  https://clip2net.com/s/3T0vb8K
 *  */

// 5. Добавить новое поле skype в форму регистрации.
function skype_registation_fields() {
    ?>
    <p class="form-row form-row-wide">
        <label for="billing_skype"><?php _e('Skype', 'woocommerce'); ?> <span class="required">*</span></label></label>
    <input type="text" class="input-text" name="billing_skype" id="billing_skype" value="<?php echo (isset($_POST['billing_skype']) && empty($_POST['billing_skype'])) ? esc_attr_e($_POST['billing_skype']) : ''; ?>" />
    </p>
    <div class="clear"></div>
    <?php
}

add_action('woocommerce_register_form_start', 'skype_registation_fields');

function skype_validate_reg_form_fields($username, $email, $validation_errors) {
    if (isset($_POST['billing_skype']) && empty($_POST['billing_skype'])) {
        $validation_errors->add('billing_skype_error', __('Skype is required!', 'woocommerce'));
    }
    return $validation_errors;
}

add_action('woocommerce_register_post', 'skype_validate_reg_form_fields', 10, 3);

function skype_save_registration_form_fields($customer_id) {
    if (isset($_POST['billing_skype'])) {
        update_user_meta($customer_id, 'billing_skype', sanitize_text_field($_POST['billing_skype']));
    }
}

add_action('woocommerce_created_customer', 'skype_save_registration_form_fields');

function skype_edit_account_form() {
    $user_id = get_current_user_id();
    $current_user = get_userdata($user_id);
    if (!$current_user)
        return;
    $billing_skype = get_user_meta($user_id, 'billing_skype', true);
    ?>    
    <fieldset>
        <legend>Skype information</legend>
        <p class="form-row form-row-wide">
            <label for="reg_billing_skype"><?php _e('Skype', 'woocommerce'); ?> <span class="required">*</span></label></label>
            <input type="text" class="input-text" name="billing_skype" id="reg_billing_skype" value="<?php echo esc_attr($billing_skype); ?>" />
        </p>
        <div class="clear"></div>
    </fieldset>
    <?php
}

function skype_save_account_details($user_id) {
    update_user_meta($user_id, 'billing_skype', sanitize_text_field($_POST['billing_skype']));
}

add_action('woocommerce_edit_account_form', 'skype_edit_account_form');
add_action('woocommerce_save_account_details', 'skype_save_account_details');

// 6. После регистрации пользователь должен попадать на страницу с избранными фильмами.
// P.S. не понятно что именно "избранными фильмами" если пользователь первый раз на сайте. Потому просто редирект на все фильмы
function film_register_redirect($redirect) {
    return home_url('/films');
}

add_filter('woocommerce_registration_redirect', 'film_register_redirect');

// 7. После нажатия на кнопку купить пользователь должен быть направлен на оплату paypal минуя корзину.
// P.S. так как он должен подтвердить заказ то минуя checkout не логично
function redirect_checkout_add_cart($url) {
    $url = get_permalink(get_option('woocommerce_checkout_page_id'));
    return $url;
}

add_filter('woocommerce_add_to_cart_redirect', 'redirect_checkout_add_cart');
