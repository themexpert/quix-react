jQuery(document).ready(function(){
  var appeared = false;
  jQuery("#<?php echo $id?>").on("appear", function(){
    if(appeared) return;
    appeared = true;
    var range = jQuery(this).attr('data-range');
    jQuery(this).find('.qx-nc-number').countTo({
      from: 0,
      to: range,
      speed: <?php echo $speed; ?>,
      refreshInterval: 50,
    });
  });
  jQuery("#<?php echo $id?>").appear();
});
