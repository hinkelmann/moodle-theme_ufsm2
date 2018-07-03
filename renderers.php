<?php
/**
 * Moodle's ufsm theme
 * @package    theme_ufsm
 * @copyright  2016 Núcleo de Tecnologia Educacional {@link http://nte.ufsm.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */ /*
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 */

require_once('renderers/core_renderer.php');
require_once('renderers/maintenance_renderer.php');
require_once('renderers/admin_renderer.php');
require_once('renderers/course_renderer.php');
require_once('renderers/core_user_renderer.php');

require_once('renderers/course_management.php');
require_once('renderers/block_settings_renderer.php');
require_once('renderers/enrol_renderer.php');


//Cursos
require_once('renderers/course/theme_ufsm2_format_section_renderer_base.php');
require_once('renderers/course/theme_ufsm2_format_topcoll_renderer.php');
require_once('renderers/course/theme_ufsm2_format_topics_renderer.php');
require_once('renderers/course/theme_ufsm2_format_weeks_renderer.php');

