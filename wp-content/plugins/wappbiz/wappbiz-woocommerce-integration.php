<?php
/**
 * Plugin Name: Wappbiz WooCommerce Integration
 * Description: Advanced integration with Wappbiz API for seamless order synchronization
 * Version: 2.0
 * Author: Augment Works
 * Text Domain: wappbiz-integration
 */

namespace WappbizIntegration;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}


class WappbizIntegration {
    private const OPTION_API_KEY = 'wappbiz_api_key';
    private const OPTION_ORDERS_LOG = 'wappbiz_orders_log';
    private const API_ENDPOINT = 'https://devapi.wapp.biz/api/wappbiz/wordpressOrder';

    public function __construct() {
        add_action('init', [$this, 'init_plugin']);
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('woocommerce_thankyou', [$this, 'send_order_to_api'], 10, 1);
        
    }
    
function wappbiz_add_action_links($links) {
    // Settings link
    $settings_link = '<a href="admin.php?page=wappbiz-integration">' . __('Settings', 'wappbiz-integration') . '</a>';
    // Documentation link (URL of your plugin documentation page)
    $documentation_link = '<a href="https://your-docs-link.com" target="_blank">' . __('Documentation', 'wappbiz-integration') . '</a>';

    // Add these links before the "Deactivate" link
    array_unshift($links, $settings_link);
    $links[] = $documentation_link;

    return $links;
}

   public function init_plugin() {
    load_plugin_textdomain('wappbiz-integration', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    
    // Register settings
    register_setting(
        'wappbiz_settings_group', // Settings group
        self::OPTION_API_KEY,     // Option name
        [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => ''
        ]
    );
}


    public function register_admin_menu() {
        add_menu_page(
            __('Wappbiz Integration', 'wappbiz-integration'),
            __('Wappbiz API', 'wappbiz-integration'),
            'manage_options',
            'wappbiz-integration',
            [$this, 'render_settings_page'],
            'dashicons-megaphone',
            99
        );
    }

    public function enqueue_admin_scripts($hook) {
        if ($hook === 'toplevel_page_wappbiz-integration') {
            wp_enqueue_style('wappbiz-admin-styles', plugin_dir_url(__FILE__) . 'assets/admin-styles.css', [], '2.0');
            wp_enqueue_script('wappbiz-admin-script', plugin_dir_url(__FILE__) . 'assets/admin-script.js', ['jquery'], '2.0', true);
        
        }
    }

    public function render_settings_page() {
        $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'settings';
        ?>
        <div class="wrap wappbiz-admin-page">
            <h1><?php echo esc_html__('Wappbiz WooCommerce Integration', 'wappbiz-integration'); ?></h1>
            
            <nav class="nav-tab-wrapper">
                <a href="?page=wappbiz-integration&tab=settings" 
                   class="nav-tab <?php echo $tab === 'settings' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e('Settings', 'wappbiz-integration'); ?>
                </a>
                <a href="?page=wappbiz-integration&tab=logs" 
                   class="nav-tab <?php echo $tab === 'logs' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e('API Logs', 'wappbiz-integration'); ?>
                </a>
            </nav>

            <div class="tab-content">
                <?php 
                if ($tab === 'settings') {
                    $this->render_settings_tab();
                } else {
                    $this->render_logs_tab();
                }
                ?>
            </div>
        </div>
        <?php
    }

    private function render_settings_tab() {
        $api_key = get_option(self::OPTION_API_KEY, '');
        ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('wappbiz_settings_group');
            do_settings_sections('wappbiz_settings_group');
            ?>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('Wappbiz API Key', 'wappbiz-integration'); ?></th>
                    <td>
                        <input 
                            type="text" 
                            name="<?php echo esc_attr(self::OPTION_API_KEY); ?>" 
                            value="<?php echo esc_attr($api_key); ?>" 
                            class="regular-text"
                            placeholder="<?php esc_attr_e('Enter your Wappbiz API Key', 'wappbiz-integration'); ?>"
                        />
                        <p class="description">
                            <?php esc_html_e('API key required for order synchronization', 'wappbiz-integration'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings', 'wappbiz-integration'), 'primary'); ?>
        </form>
        <?php
    }

    private function render_logs_tab() {
        $orders_log = get_option(self::OPTION_ORDERS_LOG, []);
        ?>
        <div class="logs-container">
            <?php if (empty($orders_log)): ?>
                <div class="no-logs">
                    <?php esc_html_e('No API logs available', 'wappbiz-integration'); ?>
                </div>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Order ID', 'wappbiz-integration'); ?></th>
                            <th><?php esc_html_e('Status', 'wappbiz-integration'); ?></th>
                            <th><?php esc_html_e('Message', 'wappbiz-integration'); ?></th>
                            <th><?php esc_html_e('Timestamp', 'wappbiz-integration'); ?></th>
                            <th><?php esc_html_e('Actions', 'wappbiz-integration'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders_log as $log): ?>
                            <tr>
                                <td><?php echo esc_html($log['order_id']); ?></td>
                                <td><?php echo esc_html(ucfirst($log['status'])); ?></td>
                                <td><?php echo esc_html($log['message']); ?></td>
                                <td><?php echo esc_html($log['timestamp']); ?></td>
                                <td>
                                    <button 
                                        class="button button-small view-response" 
                                        data-response="<?php echo esc_attr($log['api_response']); ?>">
                                        <?php esc_html_e('View Details', 'wappbiz-integration'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div id="response-modal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close">&times;</span>
                <pre id="response-text"></pre>
            </div>
        </div>
        <?php
    }

    public function send_order_to_api($order_id) {
        $api_key = get_option(self::OPTION_API_KEY);
        if (empty($api_key)) {
            $this->log_error($order_id, 'API key not configured');
            return;
        }

        $order = wc_get_order($order_id);
        if (!$order) {
            $this->log_error($order_id, 'Invalid order');
            return;
        }

        $order_data = [
            'order_id' => $order->get_id(),
            'amount'   => $order->get_total(),
            'status'   => $order->get_status(),
        ];

        $response = wp_remote_post(
            self::API_ENDPOINT . '?apikey=' . $api_key,
            [
                'body'    => wp_json_encode($order_data),
                'headers' => ['Content-Type' => 'application/json'],
                'timeout' => 30
            ]
        );

        $this->process_api_response($order, $response);
    }

   private function process_api_response($order, $response) {
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    
    // Ensure we capture the full response as a structured array
    $full_response = [
        'status_code' => $response_code,
        'body' => $response_body ? json_decode($response_body, true) : null,
        'headers' => wp_remote_retrieve_headers($response)
    ];

    try {
        $response_data = json_decode($response_body, true);
        $message = $response_data['message'] ?? 'No message provided';
    } catch (Exception $e) {
        $message = 'Error parsing response';
    }

    $status = $response_code === 200 ? 'success' : 'error';

    $this->log_order_response(
        $order->get_id(), 
        $status, 
        $message, 
        wp_json_encode($full_response)
    );
}

    private function log_order_response($order_id, $status, $message, $api_response) {
        $orders_log = get_option(self::OPTION_ORDERS_LOG, []);
        
        $orders_log[] = [
            'order_id'     => $order_id,
            'status'       => $status,
            'message'      => $message,
            'api_response' => $api_response,
            'timestamp'    => current_time('mysql')
        ];

        update_option(self::OPTION_ORDERS_LOG, $orders_log);
    }

    private function log_error($order_id, $error_message) {
        error_log("Wappbiz Integration Error: {$error_message} for Order ID {$order_id}");
    }

    public static function activate() {
        // Future: Add any necessary setup on plugin activation
    }

    public static function deactivate() {
        // Cleanup: Optional method to remove data on deactivation
        delete_option(self::OPTION_API_KEY);
        delete_option(self::OPTION_ORDERS_LOG);
    }
}


// Initialize Plugin
add_action('plugins_loaded', function() {
    if (class_exists('WooCommerce')) {
        new WappbizIntegration();
    }
});

register_activation_hook(__FILE__, ['\WappbizIntegration\WappbizIntegration', 'activate']);
register_deactivation_hook(__FILE__, ['\WappbizIntegration\WappbizIntegration', 'deactivate']);