<?php
/**
 * Moodle's ufsm theme
 * @package    theme_ufsm
 * @copyright  2016 Núcleo de Tecnologia Educacional {@link http://nte.ufsm.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . "/course/format/weeks/renderer.php");

class theme_ufsm2_format_weeks_renderer extends format_weeks_renderer
{
    /**
     * Generate the content to displayed on the left part of a section
     * before course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return string HTML to output.
     */
    protected function section_left_content($section, $course, $onsectionpage)
    {
        $o = $this->output->spacer();

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (course_get_format($course)->is_section_current($section)) {
                $o .= get_accesshide(get_string('currentsection', 'format_' . $course->format));
            }
        }

        return $o;
    }

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list()
    {
        return html_writer::start_tag('ul', ['class' => 'weeks panel-group', 'id' => '', 'role' => "tablist", 'aria-multiselectable' => "true"]);
    }

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list()
    {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the display of the header part of a section before
     * course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a single-section page
     * @param int $sectionreturn The section to return to after an action
     * @return string HTML to output.
     */
    protected function section_header($section, $course, $onsectionpage, $sectionreturn = null)
    {
        global $PAGE;

        $o = '';
        $currenttext = '';
        $sectionstyle = '';
        $sectionIsOpen = ' in';
        $leftcontent = '';
        $rightcontent = '';

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            } else if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current';
            }
        }

        $o .= html_writer::start_tag('li', array('id' => 'section-' . $section->section,
            'class' => 'section main panel panel-default' . $sectionstyle, 'role' => 'region',
            'aria-label' => get_section_name($course, $section)));

        // Create a span that contains the section title to be used to create the keyboard section move menu.
        $o .= html_writer::tag('span', get_section_name($course, $section), array('class' => 'hidden sectionname'));


        // When not on a section page, we display the section titles except the general section if null
        $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

        // When on a section page, we only display the general section title, if title is not the default one
        $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

        $classes = ' accesshide';
        if ($hasnamenotsecpg || $hasnamesecpg) {
            $classes = '';
            if (!$PAGE->user_is_editing()) {
           //     $sectionIsOpen = '';

            }
        }
        if ($PAGE->user_is_editing()) {
            $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
            $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
        }


        // panel heading start
        $o .= html_writer::start_div('container-fluid', ['role' => 'tab', 'id' => 'header-section-' . $section->section]);
        $linkAttr = [
            'role' => 'botton',
           // 'title'=>"Clique para abrir ".$this->section_title($section, $course),
            'class' => 'accordion-toggle collapsed',
            'data-toggle' => 'collapse',
            'data-parent' => '#accordion',
            'aria-expanded' => "true",
            'aria-controls' => 'corpo-section-' . $section->section
        ];
        $link = html_writer::link('#corpo-section-' . $section->section, $this->section_title($section, $course), $linkAttr);

        $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side pull-left'));
        $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side pull-right'));

        if($PAGE->user_is_editing()){
            $o .= html_writer::tag('h4', $this->section_title($section, $course));
        }else{
            $o .= $this->output->heading($link, 4, 'sectionname' . $classes);
        }



       $o .= html_writer::end_div();
        //panel heading end


        $divAttr = [
            'id' => 'corpo-section-' . $section->section,
            'role' => 'tabpanel',
            'aria-labelledby' => 'header-section-' . $section->section,
        ];

        $o .= html_writer::start_div("panel-body panel-collapse collapse $sectionIsOpen", $divAttr);
        $o .= html_writer::start_tag('div', ['class' => 'content panel-body']);
        $o .= html_writer::start_tag('div', ['class' => 'summary']);
        $o .= $this->format_summary_text($section);
        $o .= html_writer::end_tag('div');

        $context = context_course::instance($course->id);
        $o .= $this->section_availability_message($section,
              has_capability('moodle/course:viewhiddensections', $context));

        return $o;
    }

    /**
     * Generate the display of the footer part of a section
     *
     * @return string HTML to output.
     */
    protected function section_footer()
    {
        $o = html_writer::end_tag('div');
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');

        return $o;
    }
}