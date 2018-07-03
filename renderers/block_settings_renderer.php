<?php
/**
 * Moodle's ufsm theme
 * @package    theme_ufsm
 * @copyright  2016 Núcleo de Tecnologia Educacional {@link http://nte.ufsm.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/blocks/settings/renderer.php");

class theme_ufsm2_block_settings_renderer extends block_settings_renderer {

    public function search_form(moodle_url $formtarget, $searchvalue) {
        $content = html_writer::start_tag('form',
            array(
                'class' => 'adminsearchform',
                'method' => 'get',
                'action' => $formtarget,
                'role' => 'search',
            )
        );
        $content .= html_writer::start_div('input-group');
        $content .= html_writer::empty_tag('input',
            array(
                'id' => 'adminsearchquery',
                'type' => 'text',
                'name' => 'query',
                'class' => 'form-control',
                'placeholder' => s(get_string('searchinsettings', 'admin')),
                'value' => s($searchvalue),
            )
        );
        $content .= html_writer::start_span('input-group-btn');
        $content .= html_writer::tag('button', s(get_string('go')), array('type' => 'submit', 'class' => 'btn btn-default'));
        $content .= html_writer::end_span();
        $content .= html_writer::end_div();
        $content .= html_writer::end_tag('form');
        return $content;
    }

}
