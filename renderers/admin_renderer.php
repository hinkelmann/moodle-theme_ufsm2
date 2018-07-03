<?php
/**
 * Moodle's ufsm theme
 * @package    theme_ufsm
 * @copyright  2016 Núcleo de Tecnologia Educacional {@link http://nte.ufsm.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/' . $CFG->admin . "/renderer.php");


class theme_ufsm2_core_admin_renderer extends core_admin_renderer {

    protected function maturity_info($maturity) {
        if ($maturity == MATURITY_STABLE) {
            return ''; // No worries.
        }

        if ($maturity == MATURITY_ALPHA) {
            $level = 'notifyproblem';
        } else {
            $level = 'notifywarning';
        }

        $maturitylevel = get_string('maturity' . $maturity, 'admin');
        $warningtext = get_string('maturitycoreinfo', 'admin', $maturitylevel);
        $doclink = $this->doc_link('admin/versions', get_string('morehelp'));

        return $this->notification($warningtext . ' ' . $doclink, $level);
    }

    protected function maturity_warning($maturity) {
        if ($maturity == MATURITY_STABLE) {
            return ''; // No worries.
        }

        $maturitylevel = get_string('maturity' . $maturity, 'admin');
        $maturitywarning = get_string('maturitycorewarning', 'admin', $maturitylevel);
        $maturitywarning .= $this->doc_link('admin/versions', get_string('morehelp'));

        return $this->notification($maturitywarning, 'notifyproblem');
    }

    protected function warning($message, $type = 'warning') {
        if ($type == 'warning') {
            return $this->notification($message, 'notifywarning');
        } else if ($type == 'error') {
            return $this->notification($message, 'notifyproblem');
        }
    }

    /**
     * Output a warning message, of the type that appears on the admin notifications page.
     * @param string $message the message to display.
     * @param string $type type class
     * @return string HTML to output.
     */
    /* protected function warning($message, $type = 'warning') {
        return $this->box($message, 'generalbox admin' . $type);
    } */

    protected function test_site_warning($testsite) {
        if (!$testsite) {
            return '';
        }
        $warningtext = get_string('testsiteupgradewarning', 'admin', $testsite);
        return $this->notification($warningtext, 'notifyproblem');
    }

    protected function release_notes_link() {
        $releasenoteslink = get_string('releasenoteslink', 'admin', 'http://docs.moodle.org/dev/Releases');
        return $this->notification($releasenoteslink, 'notifymessage');
    }

    public function plugins_check_table(core_plugin_manager $pluginman, $version, array $options = array()) {
        $html = parent::plugins_check_table($pluginman, $version, $options);

        $replacements = array(
            'generaltable' => 'table table-striped',
            'status-missing' => 'danger',
            'status-downgrade' => 'danger',
            'status-upgrade' => 'info',
            'status-delete' => 'info',
            'status-new' => 'success',
        );

        $find = array_keys($replacements);
        $replace = array_values($replacements);

        return str_replace($find, $replace, $html);
    }

    public function environment_check_table($result, $environment) {
        $html = parent::environment_check_table($result, $environment);

        $replacements = array(
            '<span class="ok">' => '<span class="label label-success">',
            '<span class="warn">' => '<span class="label label-warning">',
            '<span class="error">' => '<span class="label label-danger">',
            '<p class="ok">' => '<p class="text-success">',
            '<p class="warn">' => '<p class="text-warning">',
            '<p class="error">' => '<p class="text-danger">',
        );

        $find = array_keys($replacements);
        $replace = array_values($replacements);

        return str_replace($find, $replace, $html);
    }
}
