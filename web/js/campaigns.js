
$(document).ready(function(){
  
  $('.sf_admin_action_export a')
    .attr('target', '_blank')
    .click(function() {
      $('#transition .close').click();
  });
});
