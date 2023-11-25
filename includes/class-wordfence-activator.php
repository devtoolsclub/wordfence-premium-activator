<?php

defined('ABSPATH') || exit;

class Wordfence_Activator {
    const REMAINING_DAYS = 3650; // 10 years

    public function run() {
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function init() {
        if (!class_exists('wfLicense')) {
            return;
        }

        try {
            $this->set_wordfence_config();
        } catch (Exception $exception) {
            add_action('admin_notices', function () use ($exception) {
                $this->error_notice($exception->getMessage());
            });
        }
    }

    private function set_wordfence_config() {
        wfOnboardingController::_markAttempt1Shown();
        wfConfig::set('onboardingAttempt3', wfOnboardingController::ONBOARDING_LICENSE);
        if (empty(wfConfig::get('apiKey'))) {
            wordfence::ajax_downgradeLicense_callback();
        }
        wfConfig::set('isPaid', true);
        wfConfig::set('keyType', wfLicense::KEY_TYPE_PAID_CURRENT);
        wfConfig::set('premiumNextRenew', time() + self::REMAINING_DAYS * DAY_IN_SECONDS);
        wfWAF::getInstance()->getStorageEngine()->setConfig('wafStatus', wfFirewall::FIREWALL_MODE_ENABLED);
    }

    private function error_notice($message) {
        ?>
        <div class="notice notice-error">
            <p><?php echo esc_html($message); ?></p>
        </div>
        <?php
    }
}
