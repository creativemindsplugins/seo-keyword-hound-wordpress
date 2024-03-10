<?php
use com\cminds\seokeywords\App;
use com\cminds\seokeywords\plugin;
echo do_shortcode( sprintf( '[cminds_free_guide id=%s]', App::SLUG ) );