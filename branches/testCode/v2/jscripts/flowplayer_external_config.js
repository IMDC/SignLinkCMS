/** 
 * Flowplayer Configuration File
 */  
var conf = { 
     
    // default clip configurations 
    noplay: { 
         
        autoPlay: false, 
        autoBuffering: true, 
		
        //baseUrl: 'http://blip.tv/file/get', 
     
        onBegin: function() { 
            this.getControls().fadeIn(4000); 
        }, 
    },    

    yesplay: { 
         
        autoPlay: true, 
        autoBuffering: true, 
		
        //baseUrl: 'http://blip.tv/file/get', 
     
        onBegin: function() { 
            this.getControls().fadeIn(4000); 
        }, 
    },    


    small: {
        backgroundColor: '#000000',
        backgroundGradient: 'low',
        autoHide: 'always',
        hideDelay: 2000,
        all: false,
        scrubber: true,
        //mute: true,
        fullscreen: true,
        height: 14,
        progressColor: '#FFFF00',
        progressGradient: 'medium',
        bufferColor: '#333333'
    },

    big: {
        backgroundColor: '#000000',
        backgroundGradient: 'low',
        autoHide: 'always',
        hideDelay: 2000,
        all: false,
        scrubber: true,
        mute: true,
        fullscreen: true,
        height: 25,
        progressColor: '#FFFF00',
        progressGradient: 'medium',
        bufferColor: '#333333'
    }
}
