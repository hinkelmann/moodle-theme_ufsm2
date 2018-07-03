<?php
/**
 * Moodle's ufsm theme
 * @package    theme_ufsm
 * @copyright  2016 Núcleo de Tecnologia Educacional {@link http://nte.ufsm.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Requires V2.6.1.3+ of the Collapsed Topics format.
if (file_exists("$CFG->dirroot/course/format/topcoll/renderer.php")) {
    include_once($CFG->dirroot . "/course/format/topcoll/renderer.php");

    class theme_ufsm2_format_topcoll_renderer extends format_topcoll_renderer
    {
        protected function get_row_class()
        {
            return 'row';
        }

        protected function get_column_class($columns)
        {
            $colclasses = array(
                1 => 'col-sm-12 col-md-12 col-lg-12',
                2 => 'col-sm-6 col-md-6 col-lg-6',
                3 => 'col-md-4 col-lg-4',
                4 => 'col-lg-3');

            return $colclasses[$columns];
        }
    }
}