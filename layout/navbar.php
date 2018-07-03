<nav role="navigation" class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <?php echo $OUTPUT->navbar_brand(); ?>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <?php echo $OUTPUT->menu_principal() ?>
        </div>
    </div>
</nav>