<?php

  namespace stm;

  use STM_LMS_Helpers;
  use STM_LMS_User;

  class User {

    public function __construct() {
      /* Load parent theme styles */
      add_filter('locale_stylesheet_uri', [$this, 'child_theme_cfg_locale_css']);
      add_action('wp_enqueue_scripts', [$this, 'child_theme_cfg_parent_css'], 10);
      add_action('wp_enqueue_scripts', [$this, 'child_theme_configurator_css'], 10);

      /* Enqueue child theme styles and scripts */
      add_action('wp_enqueue_scripts', [$this, 'child_theme_enqueue_scripts']);

      /* Backend registration hooks */
      add_action('user_new_form', [$this, 'admin_registration_form']); // Show custom fields in admin area
      add_action('user_profile_update_errors', [$this, 'user_profile_update_errors'], 10, 3); // Validate user input
      add_action('edit_user_profile', [$this, 'show_extra_profile_fields']); // Save user

      add_action('show_user_profile', [$this, 'show_extra_profile_fields']);
      add_action('personal_options_update', [$this, 'update_profile_fields']);
      add_action('edit_user_profile_update', [$this, 'update_profile_fields']);
      add_action('edit_user_created_user', [$this, 'update_profile_fields']); // Save (update user meta) on create new user page
      add_action('user_register', [$this, 'frontend_register_new_user']);
      add_action('show_custom_fields', [$this, 'display_custom_fields']);
      add_action('wp_ajax_stm_lms_save_user_info', [$this, 'save_user_data']);
    }

    public  function child_theme_cfg_locale_css($uri) {
      if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css'))
        $uri = get_template_directory_uri() . '/rtl.css';
      return $uri;
    }

    public  function child_theme_cfg_parent_css() {
      wp_enqueue_style('child_theme_cfg_parent', trailingslashit(get_template_directory_uri()) . 'style.css', array('select2', 'fancybox', 'animate', 'stm_theme_styles', 'stm-stm_layout_styles-online-light', 'stm_theme_styles_animation', 'stm-headers-header_2', 'stm-headers_transparent-header_2_transparent'));
    }

    public  function child_theme_configurator_css() {
      if (!file_exists(trailingslashit(get_stylesheet_directory()) . 'assets/css/select2.min.css')):
        wp_deregister_style('select2');
        wp_register_style('select2', trailingslashit(get_template_directory_uri()) . 'assets/css/select2.min.css');
      endif;
      if (!file_exists(trailingslashit(get_stylesheet_directory()) . 'assets/css/jquery.fancybox.css')):
        wp_deregister_style('fancybox');
        wp_register_style('fancybox', trailingslashit(get_template_directory_uri()) . 'assets/css/jquery.fancybox.css');
      endif;
      if (!file_exists(trailingslashit(get_stylesheet_directory()) . 'assets/css/animate.css')):
        wp_deregister_style('animate');
        wp_register_style('animate', trailingslashit(get_template_directory_uri()) . 'assets/css/animate.css');
      endif;
      if (!file_exists(trailingslashit(get_stylesheet_directory()) . 'assets/css/styles.css')):
        wp_deregister_style('stm_theme_styles');
        wp_register_style('stm_theme_styles', trailingslashit(get_template_directory_uri()) . 'assets/css/styles.css');
      endif;
      if (!file_exists(trailingslashit(get_stylesheet_directory()) . 'assets/css/vc_modules/stm_layout_styles/online-light.css')):
        wp_deregister_style('stm-stm_layout_styles-online-light');
        wp_register_style('stm-stm_layout_styles-online-light', trailingslashit(get_template_directory_uri()) . 'assets/css/vc_modules/stm_layout_styles/online-light.css');
      endif;
      if (!file_exists(trailingslashit(get_stylesheet_directory()) . 'assets/css/animation.css')):
        wp_deregister_style('stm_theme_styles_animation');
        wp_register_style('stm_theme_styles_animation', trailingslashit(get_template_directory_uri()) . 'assets/css/animation.css');
      endif;
      if (!file_exists(trailingslashit(get_stylesheet_directory()) . 'assets/css/vc_modules/headers/header_2.css')):
        wp_deregister_style('stm-headers-header_2');
        wp_register_style('stm-headers-header_2', trailingslashit(get_template_directory_uri()) . 'assets/css/vc_modules/headers/header_2.css');
      endif;
      if (!file_exists(trailingslashit(get_stylesheet_directory()) . 'assets/css/vc_modules/headers_transparent/header_2_transparent.css')):
        wp_deregister_style('stm-headers_transparent-header_2_transparent');
        wp_register_style('stm-headers_transparent-header_2_transparent', trailingslashit(get_template_directory_uri()) . 'assets/css/vc_modules/headers_transparent/header_2_transparent.css');
      endif;
      wp_enqueue_style('child_theme_cfg_separate', trailingslashit(get_stylesheet_directory_uri()) . 'custom-style.css', array('child_theme_cfg_parent', 'stm_theme_style', 'language_center'));
    }

    function child_theme_enqueue_scripts(){
      wp_enqueue_script( 'imask', get_stylesheet_directory_uri() . '/assets/js/input-mask.js');
      wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/assets/js/main.js', ['imask']);
    }


    /**
     * Add custom data fields on admin create user page
     *
     * @param $operation
     */
    public function admin_registration_form($operation) {
      if ('add-new-user' !== $operation) {
        return;
      }

      $phone = !empty($_POST['phone']) ? $_POST['phone'] : '';
      $country = !empty($_POST['country']) ? $_POST['country'] : '';
      $city = !empty($_POST['city']) ? $_POST['city'] : '';

      ?>
      <h3><?php esc_html_e('Personal Information', 'masterstudy-lms-learning-management-system'); ?></h3>

      <table class="form-table">
        <tr>
          <th>
            <label for="phone">
              <?php esc_html_e('Phone', 'masterstudy-lms-learning-management-system'); ?>
            </label>
            <span class="description">
                  <?php esc_html_e('(required)', 'masterstudy-lms-learning-management-system'); ?>
                </span>
          </th>
          <td>
            <input type="tel" id="phone" name="phone" value="<?php echo esc_attr($phone); ?>"/>
          </td>
        </tr>
        <tr>
          <th>
            <label for="country">
              <?php esc_html_e('Country', 'masterstudy-lms-learning-management-system'); ?>
            </label>
            <span class="description">
                  <?php esc_html_e('(required)', 'masterstudy-lms-learning-management-system'); ?>
                </span>
          </th>
          <td>
            <input type="text" id="country" name="country" value="<?php echo esc_attr($country); ?>"/>
          </td>
        </tr>
        <tr>
          <th>
            <label for="city">
              <?php esc_html_e('City', 'masterstudy-lms-learning-management-system'); ?>
            </label>
            <span class="description">
                  <?php esc_html_e('(required)', 'masterstudy-lms-learning-management-system'); ?>
                </span>
          </th>
          <td>
            <input type="text" id="city" name="city" value="<?php echo esc_attr($city); ?>"/>
          </td>
        </tr>

      </table>
      <?php
    }

    /**
     * Show addition fields in admin page
     *
     * @param $user
     */
    public function show_extra_profile_fields($user) {
      $phone = get_the_author_meta('phone', $user->ID);
      $country = get_the_author_meta('country', $user->ID);
      $city = get_the_author_meta('city', $user->ID);
      ?>
      <h3><?php esc_html_e('Additional Information', 'masterstudy'); ?></h3>

      <table class="form-table">
        <tr>
          <th><label for="phone"><?php esc_html_e('Phone', 'masterstudy'); ?></label></th>
          <td>
            <input type="tel"
                   id="phone"
                   name="phone"
                   value="<?php echo esc_attr($phone); ?>"
            />
          </td>
        </tr>
        <tr>
          <th><label for="country"><?php esc_html_e('Country', 'masterstudy'); ?></label></th>
          <td>
            <input type="text"
                   id="country"
                   name="country"
                   value="<?php echo esc_attr($country); ?>"
            />
          </td>
        </tr>
        <tr>
          <th><label for="city"><?php esc_html_e('City', 'masterstudy'); ?></label></th>
          <td>
            <input type="text"
                   id="city"
                   name="city"
                   value="<?php echo esc_attr($city); ?>"
            />
          </td>
        </tr>
      </table>

      <?php
    }

    /**
     * Show errors on admin edit page
     *
     * @param $errors
     * @param $update
     * @param $user
     */
    public function user_profile_update_errors($errors, $update, $user) {
      if (empty($_POST['phone'])) {
        $errors->add('phone_error', __('<strong>Error</strong>: Please enter your phone number.', 'masterstudy-lms-learning-management-system'));
      }
      if (empty($_POST['country'])) {
        $errors->add('country_error', __('<strong>Error</strong>: Please enter your country.', 'masterstudy-lms-learning-management-system'));
      }
      if (empty($_POST['city'])) {
        $errors->add('city_error', __('<strong>Error</strong>: Please enter your city.', 'masterstudy-lms-learning-management-system'));
      }
    }

    /**
     * Update (on admin edit page) user meta data
     *
     * @param $user_id
     * @return false
     */
    public function update_profile_fields($user_id) {
      if (!current_user_can('edit_user', $user_id)) {
        return false;
      }

      if (!empty($_POST['phone'])) {
        update_user_meta($user_id, 'phone', $_POST['phone']);
      }
      if (!empty($_POST['country'])) {
        update_user_meta($user_id, 'country', $_POST['country']);
      }
      if (!empty($_POST['city'])) {
        update_user_meta($user_id, 'city', $_POST['city']);
      }
    }

    /**
     * Custom fields list
     *
     * @return array[]
     */
    public static function custom_fields() {
      return [
        'phone' => [
          'label' => esc_html__('Phone', 'masterstudy-lms-learning-management-system'),
          'type' => 'text'
        ],
        'country' => [
          'label' => esc_html__('Country', 'masterstudy-lms-learning-management-system'),
          'type' => 'text'
        ],
        'city' => [
          'label' => esc_html__('City', 'masterstudy-lms-learning-management-system'),
          'type' => 'text'
        ],

      ];
    }

    /**
     * Show errors
     *
     * @param $fields
     * @param $request_data
     */
    public static function show_error_message($fields, $request_data) {
      if (!current_user_can('administrator') && !is_admin()) {

        $response = [
          'message' => '',
          'status' => 'error'
        ];

        foreach ($fields as $field_key => $field) {
          if (empty($request_data[$field_key])) {
            $response['message'] = sprintf(esc_html__('%s field is required', 'masterstudy-lms-learning-management-system'), $field['label']);
            wp_send_json($response);
            die;
          } else {
            $request_data[$field_key] = STM_LMS_Helpers::sanitize_fields($request_data[$field_key], $field['type']);
            if (empty($request_data[$field_key])) {
              $response['message'] = sprintf(esc_html__('Please enter valid %s field', 'masterstudy-lms-learning-management-system'), $field['label']);
              wp_send_json($response);
              die;
            }
          }
        }
      }
    }

    /**
     * Add (update meta) custom fields
     *
     * @param $user_id
     * @param $fields
     * @param $request_data
     */
    public static function add_custom_fields($user_id, $fields, $request_data) {
      foreach ($fields as $field_key => $field) {
        update_user_meta($user_id, $field_key, sanitize_text_field($request_data[$field_key]));
      }
    }

    /**
     * Register a new user (frontend)
     *
     * @param $user_id
     */
    public function frontend_register_new_user($user_id) {

      $request_body = file_get_contents('php://input');

      $data = json_decode($request_body, true);

      $fields = self::custom_fields();

      self::show_error_message($fields, $data);

      self::add_custom_fields($user_id, $fields, $data);
    }

    /**
     * Display custom fields on user's profile page
     */
    public function display_custom_fields() {
      ?>
      <div class="row">

        <div class="col-md-6">
          <div class="form-group">
            <label
              class="heading_font"><?php esc_html_e('Country', 'masterstudy-lms-learning-management-system'); ?></label>
            <input v-model="data.meta.country"
                   class="form-control"
                   placeholder="<?php esc_html_e('Enter your country', 'masterstudy-lms-learning-management-system') ?>"/>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label
              class="heading_font"><?php esc_html_e('city', 'masterstudy-lms-learning-management-system'); ?></label>
            <input v-model="data.meta.city"
                   class="form-control"
                   placeholder="<?php esc_html_e('Enter your city', 'masterstudy-lms-learning-management-system') ?>"/>
          </div>
        </div>

      </div>
      <div class="row">

        <div class="col-md-6">
          <div class="form-group">
            <label
              class="heading_font"><?php esc_html_e('Phone', 'masterstudy-lms-learning-management-system'); ?></label>
            <input v-model="data.meta.phone"
                   class="form-control"
                   placeholder="<?php esc_html_e('Enter your phone', 'masterstudy-lms-learning-management-system') ?>"/>
          </div>
        </div>

      </div>

    <?php }

    /**
     * Save user data on user profile page
     */
    public function save_user_data() {
      $user = STM_LMS_User::get_current_user();
      if (empty($user['id'])) die;
      $user_id = $user['id'];
      echo $user_id;

      $fields = self::custom_fields();
      $data = array();

      foreach ($fields as $field_name => $field) {
        if (isset($_GET[$field_name])) {
          $new_value = sanitize_text_field($_GET[$field_name]);
          update_user_meta($user_id, $field_name, $new_value);
          $data[$field_name] = $new_value;
        }
      }

      $r = array(
        'data' => $data,
        'status' => 'success',
        'message' => esc_html__('Successfully saved', 'masterstudy-lms-learning-management-system')
      );

      wp_send_json($r);

    }
  }

  new User();