<?php
use com\cminds\seokeywords\App;
use com\cminds\seokeywords\plugin;
echo do_shortcode( sprintf( '[cminds_upgrade_box id=%s]', App::SLUG ) );