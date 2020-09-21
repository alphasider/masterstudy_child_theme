<?php

  namespace stm;

  use STM_LMS_Helpers;

  class User {

    public function __construct() {

      /* Backend registration hooks */
      add_action('user_new_form', [$this, 'admin_registration_form']); // Show custom fields in admin area
      add_action('user_profile_update_errors', [$this, 'user_profile_update_errors'], 10, 3); // Validate user input
      add_action('edit_user_profile', [$this, 'show_extra_profile_fields']); // Save user

      add_action('show_user_profile', [$this, 'show_extra_profile_fields']);
      add_action('personal_options_update', [$this, 'update_profile_fields']);
      add_action('edit_user_profile_update', [$this, 'update_profile_fields']);

      add_action('edit_user_created_user', [$this, 'update_profile_fields']); // Save (update user meta) on create new user page

      add_action('user_register', [$this, 'frontend_register_new_user']);

    }

    /**
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

      echo '<pre>';
      print_r($phone);
      print_r($city);
      print_r($country);
      echo '</pre>';
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
     * @param $user_id
     * @param $fields
     * @param $request_data
     */
    public static function add_custom_fields($user_id, $fields, $request_data) {
      foreach ($fields as $field_key => $field) {
        update_user_meta($user_id, $field_key, sanitize_text_field($request_data[$field_key]));
      }
    }

    public function frontend_register_new_user($user_id) {

      $request_body = file_get_contents('php://input');

      $data = json_decode($request_body, true);

      $fields = self::custom_fields();

      self::show_error_message($fields, $data);

      self::add_custom_fields($user_id, $fields, $data);
    }
  }

  new User();