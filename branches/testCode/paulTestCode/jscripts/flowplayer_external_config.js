/** 
 * Flowplayer Configuration File
 */  

var conf = { 

    // default clip configurations 
    defaults: {

        autoPlay: false,
        autoBuffering: true,
        onBegin: function() { 
            this.getControls().fadeIn(4000); 
        }
    },

    noplay: { 
         
        autoPlay: false, 
     
        onBegin: function() { 
            this.getControls().fadeIn(4000); 
        } 
    },    

    yesplay: { 
         
        autoPlay: true, 
     
        onBegin: function() { 
            this.getControls().fadeIn(4000); 
        } 
    },    


    small: {

        height: 14,
        scaling: 'fit',
        backgroundColor: 'transparent',
        backgroundGradient: "low",
        autoHide: {
          fullscreenOnly: false, 
          hideDelay: 2000
        },
        all: false,
        scrubber: true,
        mute: true,
        fullscreen: true,
        progressColor: '#FFFF00',
        progressGradient: 'medium',
        bufferColor: '#333333',
        tooltips: {
          buttons: true,
          fullscreen: 'Show in fullscreen'
        }
    },

    big: {
        height: 25,
        scaling: 'fit',
        backgroundColor: 'transparent',
        backgroundGradient: "low",
        autoHide: {
          fullscreenOnly: false, 
          hideDelay: 2000
        },
        all: false,
        play: true,
        volume: true,
        mute: true,
        timeFontSize: 9,
        slowForward: true,
        scrubber: true,
        fullscreen: true,
        progressColor: '#FFFF00',
        progressGradient: 'medium',
        bufferColor: '#333333',
        tooltips: {
          buttons: true,
          fullscreen: 'Show in fullscreen'
        }
    }
}
