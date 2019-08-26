<?php
if(checkQuixIsBuilderMode()) return;

if (!defined('QX_ELEMENT_FORM')) {
    define('QX_ELEMENT_FORM', true);
    JHtml::_('script', 'system/core.js', false, true);

    $_SESSION['quix_recaptcha'] = [
	    'first_number' => rand(1, 10),
	    'second_number' => rand(1, 10)
	];
}