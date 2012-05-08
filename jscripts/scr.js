function advanceVid() {
   $.fancybox.next();
}

$(document).ready(function() {

   var myInterval;
   var autoPlay = 1;

   $("a.slolightbox").fancybox({
      'transitionIn'  :  'elastic',
      'transitionOut' :  'elastic',
      'speedIn'       :  200, 
      'speedOut'      :  200, 
      'cyclic'        :  'true',
      'overlayColor'  :  '#000',
      'overlayOpacity':  '0.4',
      'swf'           :  '{wmode:"opaque"}',
      'height'        :  430,
      'width'         :  570,
      'onStart'       : function() {
                           $("#intro-container").hide();
                           myInterval = setInterval('advanceVid()', 70000); // auto advance the gallery after x milliseconds eg) 3000 = 3 sec
                        },
      'onClosed'      : function() {
                           $("#intro-container").show();
                           clearInterval(myInterval);
                        }
   });

});
