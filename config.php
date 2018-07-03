<?php
/**
 * Moodle's ufsm theme
 * @package    theme_ufsm
 * @copyright  2016 Núcleo de Tecnologia Educacional {@link http://nte.ufsm.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$THEME->doctype = 'html5';
$THEME->yuicssmodules = [];
$THEME->name = 'ufsm2';
$THEME->parents = [];
$THEME->sheets = 'ltr' === get_string('thisdirection', 'langconfig')?['moodle_min']:['moodle-rtl_min'];
$THEME->enable_dock = false;
$THEME->supportscssoptimisation = false;
$THEME->editor_sheets = ['editor'];
$THEME->plugins_exclude_sheets = [
    'block' => ['search_forums'],
    'tool' =>  ['customlang'],
    'mod' =>   ['feedback'],
];
$THEME->javascripts = ['blockhider'];
$THEME->javascripts_footer = ['moodlebootstrap', 'dock'];
$THEME->hidefromselector = false;
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->layouts = [
    //Layout base
    'base' => [
        'file' => 'default.php',
        'regions' => []
    ],
    //Layout padrão
    'standard' => [
        'file' => 'default.php',
        'regions' => ['side-post'],
        'defaultregion' => 'side-post',

    ],
    // Layout da página principal do curso
    'course' => [
        'file' => 'course.php',
        'regions' => ['side-post'],
        'defaultregion' => 'side-post',
        'options' => ['langmenu' => true],
    ],
    'message' => [
        'file' => 'message.php',
        'regions' => [],
    ],
    //
    'coursecategory' => [
        'file' => 'default.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
    ],
    // Layout da parte do curso
    'incourse' => [
        'file' => 'defaultPaging.php',
        'regions' => ['side-post'],
        'defaultregion' => 'side-post',
    ],
    // Layout da frontpage
    'frontpage' => [
        'file' => 'default.php',
        'regions' => ['side-post'],
        'defaultregion' => 'side-post',
        'options' => ['nonavbar' => true],
    ],
    // Layout Administrativo
    'admin' => [
        'file' => 'default.php',
        'regions' => ['side-post'],
        'defaultregion' => 'side-post',
        'options' => ['fluid' => true],
    ],
    // Layout da dashboard
    'mydashboard' => [
        'file' => 'dashboard.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
        'options' => ['langmenu' => true],
    ],
    // Layotu do perfil
    'mypublic' => [
        'file' => 'default.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
    ],
    //Layout da página de login
    'login' => [
        'file' => 'login.php',
        'regions' => [],
        'options' => ['langmenu' => true, 'nonavbar' => true],
    ],
    // Layout dos popup
    'popup' => [
        'file' => 'popup.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => true],
    ],
    // Layout sem blocos
    'frametop' => [
        'file' => 'default.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nocoursefooter' => true],
    ],
    // Layout das paginas embutidas
    'embedded' => [
        'file' => 'embedded.php',
        'regions' => []
    ],
    // Layout de manutenção/instalação
    'maintenance' => [
        'file' => 'maintenance.php',
        'regions' => [],
    ],
    // Layout de impressão
    'print' => [
        'file' => 'default.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => false],
    ],
    // Layout para redirecionamentos
    'redirect' => [
        'file' => 'embedded.php',
        'regions' => [],
    ],
    // Layout para relatórios.
    'report' => [
        'file' => 'default.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // Layout para página segura
    'secure' => [
        'file' => 'default.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre'
    ],
];
