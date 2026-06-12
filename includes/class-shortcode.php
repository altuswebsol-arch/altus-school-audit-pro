<?php
if (!defined('ABSPATH')) exit;

class Altus_Shortcode {
    public function __construct() {
        add_shortcode('altus_audit', [$this, 'form']);
        add_action('wp_enqueue_scripts', [$this, 'scripts']);
        add_action('wp_ajax_altus_run_audit', [$this, 'run_audit']);
        add_action('wp_ajax_nopriv_altus_run_audit', [$this, 'run_audit']);
    }

    public function scripts() {
        wp_enqueue_script(
            'altus-js',
            ASAP_URL . 'assets/js/altus.js',
            ['jquery'],
            time(),
            true
        );
        wp_localize_script('altus-js', 'altus_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('altus_audit_nonce')
        ]);
    }

    public function form() {
        ob_start(); ?>
        <div style="max-width:640px;padding:24px;border:1px solid #ddd;border-radius:8px;font-family:sans-serif;">
            <h2 style="margin-top:0;">Website Audit Tool</h2>
            <input type="text"
                   id="altus-url"
                   placeholder="https://yourschool.org.uk"
                   style="width:100%;padding:10px;margin-bottom:10px;box-sizing:border-box;border:1px solid #ccc;border-radius:4px;">
            <button id="altus-run"
                    style="padding:10px 24px;background:#1a1a2e;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:15px;">
                Run Audit
            </button>
            <div id="altus-result" style="margin-top:24px;"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function run_audit() {
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'altus_audit_nonce')) {
            wp_send_json_error('Security check failed');
        }

        $url = esc_url_raw($_POST['url'] ?? '');
        if (!$url) {
            wp_send_json_error('Please enter a valid URL');
        }

        require_once ASAP_PATH . 'includes/class-api.php';
        $api  = new Altus_API();
        $data = $api->get_data($url);

        if (!is_array($data) || isset($data['error'])) {
            wp_send_json_error($data['error'] ?? 'API error');
        }

        /**
         * Safely extract a 0-100 score from a device result.
         * PageSpeed returns scores as 0.0-1.0 decimals.
         * Try multiple key variants in case API returns different formats.
         */
        $score = function($device_data, $keys) {
            $cats = $device_data['lighthouseResult']['categories'] ?? [];
            foreach ((array) $keys as $key) {
                if (isset($cats[$key]['score']) && $cats[$key]['score'] !== null) {
                    return (int) round((float) $cats[$key]['score'] * 100);
                }
            }
            return null;
        };

        $mobile  = $data['mobile']  ?? [];
        $desktop = $data['desktop'] ?? [];

        // Also pass raw category keys to help debug if still N/A
        $mobile_keys  = array_keys($mobile['lighthouseResult']['categories']  ?? []);
        $desktop_keys = array_keys($desktop['lighthouseResult']['categories'] ?? []);

        wp_send_json_success([
            'url'          => $url,
            'category_keys'=> ['mobile' => $mobile_keys, 'desktop' => $desktop_keys],
            'mobile'       => [
                'performance'    => $score($mobile,  ['performance']),
                'seo'            => $score($mobile,  ['seo']),
                'accessibility'  => $score($mobile,  ['accessibility']),
                'best_practices' => $score($mobile,  ['best-practices', 'best_practices']),
            ],
            'desktop'      => [
                'performance'    => $score($desktop, ['performance']),
                'seo'            => $score($desktop, ['seo']),
                'accessibility'  => $score($desktop, ['accessibility']),
                'best_practices' => $score($desktop, ['best-practices', 'best_practices']),
            ],
        ]);
    }
}
