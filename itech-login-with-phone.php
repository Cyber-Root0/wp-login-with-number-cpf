<?php
/**
 * Plugin Name: Itech - Plugin With Phone Number + CPF
 * Plugin URI: https://itechgroup.com.br/
 * Description: Realize o login no Wordpress utilizando o celular e o CPF
 * Version: 1.1.0
 * Author: Itech Group
 * Author URI: https://itechgroup.com.br/
 * Text Domain: itech-login-with-phone
 * Domain Path: /i18n/languages/
 *
 * @package Itech
 */

 add_filter('authenticate', 'allow_login_with_cpf', 10, 3);
/**
 * allow_login_with_cpf
 *
 * @param  string $user
 * @param  string $number
 * @param  string $cpf
 * @return WC_User
 */
function allow_login_with_cpf($user, $number, $cpf) {
    if (is_a($user, 'WP_User')) {
        return $user;
    }
    
    if (!empty($number)) {
        // Assume username is CPF
		
		$cpf = format_cpf($cpf);
        $number = format_phone_number($number);
		$users = get_users(
            array(
                'role' => 'customer',
                'meta_query' => array(
                    array(
                        'key' => 'billing_phone',
                        'value' => $number,
                        'compare' => '=='
                    ),
                    array(
                        'key' => 'billing_cpf',
                        'value' => $cpf,
                        'compare' => '=='
                    )
                )
            )
        );

        var_dump($number);
     if (!empty($users)) {
            $user = $users[0];
        }
    }

    return $user;
}

/**
 * format_cpf
 *
 * @param  string $cpf
 * @return string
 */
function format_cpf($cpf) {
    // Remove any non-numeric characters
    $cpf = preg_replace('/\D/', '', $cpf);

    // Insert the mask
    $formatted_cpf = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);

    return $formatted_cpf;
}

/**
 * format_phone_number
 *
 * @param  string $phone_number
 * @return string
 */
function format_phone_number($phone_number) {
    // Remove non-numeric characters
    $phone_number = preg_replace('/\D/', '', $phone_number);
    
    // Format the phone number
    $formatted_phone_number = '(' . substr($phone_number, 0, 2) . ') ' . substr($phone_number, 2, 5) . '-' . substr($phone_number, 7, 4);
    
    return $formatted_phone_number;
}
/**
 * get string translated
 *
 * @param  string $translation
 * @param  string $orig
 * @param  string $domain
 * @return string
 */
function gettext_filter($translation, $orig, $domain) {
    switch($orig) {
        case 'Username or email address':
            $translation = "Digite o seu celular";
            break;
        case 'Password':
            $translation = 'CPF';
            break;
    }
    return $translation;
}
add_filter('gettext', 'gettext_filter', 10, 3);

/**
 * add maskinput reference
 *
 * @return void
 */
function enqueue_maskinput_script() {
    wp_enqueue_script('maskinput', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_maskinput_script');

/**
 * validation username, number mask
 *
 * @return void
 */
function add_username_mask_script() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#username').mask('(99) 99999-9999');
        });
    </script>
    <?php
}
add_action('wp_footer', 'add_username_mask_script');