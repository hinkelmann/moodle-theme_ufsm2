<?php
global $PAGE, $OUTPUT, $CFG;
$img1 = "{$CFG->wwwroot}/theme/{$CFG->theme}/pix/marcas/moodle-logo.png";
$img2 = "{$CFG->wwwroot}/theme/{$CFG->theme}/pix/marcas/nte.png";
$dt = new DateTime();
?>


<footer class="hidden-sm hidden-xs">
    <div class="container-fluid">
        <div class="relogio">
            <i class="fa fa-clock-o" aria-hidden="true"></i>
            <?php echo $dt->format('H:i'); ?>
        </div>
        <div class="col-xs-12 col-sm-12  col-md-4">
            <?php echo $OUTPUT->get_marcas(); ?>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-8">
            <div class="footer-contato">
                <span class="footer-contato-titulo"><?php echo get_string('footerContact', 'theme_ufsm2') ?> : </span>
                <a title="E-mail do suporte moodle" href="mailto:suportemoodleufsm@gmail.com"> <i
                            class="fa fa-15 fa-envelope-o"></i> suportemoodleufsm@gmail.com</a>
                | <a title="Site com os Telefones de contato do suporte moodle" target="_blank"  href="https://nte.ufsm.br/servicos/suporte-moodle"><i
                            class="fa fa-15 fa-phone"> </i> Telefone de contato </a>
            </div>
        </div>
    </div>
</footer>

<footer class="visible-sm visible-xs ">
    <div class="container">
        <div class="col-xs-6 hidden-print">
            <img src="<?php echo $img1; ?>" class="img-responsive" width="100">
        </div>
        <div class="col-xs-6 hidden-print">
            <img src="<?php echo $img2; ?>" class="img-responsive pull-right" width="100">
        </div>
        <div class="col-xs-12">
            <div class="footer-contato">
                <span class="footer-contato-titulo"><?php echo get_string('footerContact', 'theme_ufsm2') ?> </span><br>
                <a title="E-mail do suporte moodle" href="mailto:suportemoodleufsm@gmail.com">
                    <i class="fa fa-15 fa-envelope-o"></i>suportemoodleufsm@gmail.com</a><br>
                <a title="Telefone de contato do suporte moodle" target="_blank" href="https://nte.ufsm.br/servicos/suporte-moodle">
                    <i class="fa fa-15 fa-phone"> </i> Telefone de contato</a>
            </div>
        </div>
    </div>
</footer>
<?php echo $OUTPUT->standard_end_of_body_html();
function getUserIP()
{
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = $_SERVER['REMOTE_ADDR'];
    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }
    return $ip;
}
?>
<script>
function  gaExec() {
    window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
    ga('create', '<?php echo $PAGE->theme->settings->gaid ?>', 'auto');
    <?php if($USER->id > 1): ?>
    var userID = '<?php echo $PAGE->theme->settings->gaprefix . "-" . $USER->id; ?>';
    ga('set', 'userId', userID);
    ga('set', 'clientId', userID);
    <?php endif; ?>
    ga('set', 'metric1', "<?php echo getUserIP();?>");
    ga('set', 'dimension1', "<?php echo getUserIP();?>");
    ga('send', 'pageview');
}
</script>

