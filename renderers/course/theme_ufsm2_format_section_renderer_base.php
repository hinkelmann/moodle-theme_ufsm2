<?php

/**
 * Moodle's ufsm theme
 * @package    theme_ufsm
 * @copyright  2016 Núcleo de Tecnologia Educacional {@link http://nte.ufsm.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 *
include_once($CFG->dirroot . "/course/format/renderer.php");

class theme_ufsm2_format_section_renderer_base extends format_section_renderer_base
{
    protected function start_section_list()
    {
        // TODO: Implement start_section_list() method.
    }

    protected function end_section_list()
    {
        // TODO: Implement end_section_list() method.
    }

    protected function page_title()
    {
        // TODO: Implement page_title() method.
    }

    /**
     * Generate the edit controls of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of links with edit controls
     * @deprecated since Moodle 3.0 MDL-48947 - please do not use this function any more.
     * @see format_section_renderer_base::section_edit_control_items()
     *
    protected function section_edit_controls($course, $section, $onsectionpage = false)
    {
        global $PAGE;

        if (!$PAGE->user_is_editing()) {
            return array();
        }

        $controls = array();
        $items = $this->section_edit_control_items($course, $section, $onsectionpage);

        foreach ($items as $key => $item) {
            $url = empty($item['url']) ? '' : $item['url'];
            $icon = empty($item['icon']) ? '' : $item['icon'];
            $name = empty($item['name']) ? '' : $item['name'];
            $attr = empty($item['attr']) ? '' : $item['attr'];
            $class = empty($item['pixattr']['class']) ? '' : $item['pixattr']['class'];
            $alt = empty($item['pixattr']['alt']) ? '' : $item['pixattr']['alt'];
            $controls[$key] = html_writer::link(
                new moodle_url($url),
                html_writer::empty_tag('img', array(
                    'src' => $this->output->pix_url($icon),
                    'class' => "icon " . $class,
                    'alt' => $alt
                )),
                $attr);
        }

        debugging('section_edit_controls() is deprecated, please use section_edit_control_items() instead.', DEBUG_DEVELOPER);
        return $controls;

    }
}
*/