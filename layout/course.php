<?php
// This file is part of The Bootstrap Moodle theme
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

global $PAGE, $OUTPUT;
$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);

$knownregionpre = $PAGE->blocks->is_known_region('side-pre');
$knownregionpost = $PAGE->blocks->is_known_region('side-post');

$regions = bootstrap_grid($hassidepre, $hassidepost);
$PAGE->set_popup_notification_allowed(false);

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>"/>
    <?php echo $OUTPUT->standard_head_html(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimal-ui">
    <meta name="theme-color" content="<?php echo $PAGE->theme->settings->mobileColor;?>">
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<?php include 'navbar.php' ?>
<header class="moodleheader">
    <div class="container-fluid">
        <?php //echo $OUTPUT->page_heading(); ?>
        <h1>    <?php echo $OUTPUT->root_category(); ?></h1>
    </div>
</header>

<div id="page" class="container-fluid">
    <?php include 'breadcrumbs.php';?>

    <div id="page-content" class="row">

        <div id="region-main" class="<?php echo $regions['content']; ?>">
            <div class="block">
                <div class="header">
                    <div class="title">
                        <h2><i class="fa fa-book"></i>
                            <?php echo $OUTPUT->page_title(); ?>
                            <div class="pull-right">
                                <a href="#" title="Expandir todos os tópicos" class="openall">
                                <i class="fa fa-plus-square-o"></i>
                                </a>
                                <a href="#" title="Recolher todos os tópicos" class="closeall">
                                    <i class="fa fa-minus-square-o"></i>
                                </a>

                            </div>
                        </h2>
                    </div>
                </div>
            </div>
            <div id="regionmain" class="panel">

            </div>
            <?php
            echo $OUTPUT->course_content_header();
            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
            ?>
        </div>

        <?php
        if ($knownregionpre) {
            echo $OUTPUT->blocks('side-pre', $regions['pre']);
        } ?>
        <?php
        if ($knownregionpost) {
            echo $OUTPUT->blocks('side-post', $regions['post']);
        } ?>
    </div>
</div>
<?php require_once('footer.php') ?>
</body>
</html>
