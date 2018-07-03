<?php
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    //Google Analytics ID
    $name = 'theme_ufsm2/gaid';
    $title = get_string('gaField', 'theme_ufsm2');
    $description = get_string('gaFieldDesc', 'theme_ufsm2');
    $setting = new admin_setting_configtext($name, $title, $description, null);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    $name = 'theme_ufsm2/gaprefix';
    $title = get_string('gaPrefixField', 'theme_ufsm2');
    $description = get_string('gaPrefixFieldDesc', 'theme_ufsm2');
    $setting = new admin_setting_configtext($name, $title, $description, null);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    $name = 'theme_ufsm2/mobileColor';
    $title = get_string('mobileColorField', 'theme_ufsm2');
    $description = get_string('mobileColorDesc', 'theme_ufsm2');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, null);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}