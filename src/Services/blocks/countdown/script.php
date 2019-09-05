jQuery('#<?php echo $id;?>_clock').countdown('<?php echo $date .' '. $time ?>', function(event) {
    var $this = jQuery(this).html(event.strftime(''
        + '<div class="days"><span>%-D</span> <?php echo JText::_("Days"); ?></div> '
        + '<div class="hours"><span>%H</span> <?php echo JText::_("Hours"); ?></div> '
        + '<div class="minutes"><span>%M</span> <?php echo JText::_("Minutes"); ?></div> '
        + '<div class="seconds"><span>%S</span> <?php echo JText::_("Seconds"); ?></div> '
    ))
    .on('finish.countdown', function(event) {
        jQuery(this).html('<?php echo $field['offer_expired']; ?>')
                    .parent().addClass('disabled');
    });
});
