var platformFactory = function() {
	    // Platforms.........................................................

    this.platformData = [
      // Screen 1.......................................................
      {
        left: 10,
        width: 230,
        height: magicNumbers.PLATFORM_HEIGHT,
        fillStyle: 'rgb(150,190,255)',
        opacity: 1.0,
        track: 1,
        pulsate: false,
      },
      
      {
        left: 250,
        width: 100,
        height: magicNumbers.PLATFORM_HEIGHT,
        fillStyle: 'rgb(150,190,255)',
        opacity: 1.0,
        track: 2,
        pulsate: false,
      },

      {
        left: 400,
        width: 125,
        height: magicNumbers.PLATFORM_HEIGHT,
        fillStyle: 'rgb(250,0,0)',
        opacity: 1.0,
        track: 3,
        pulsate: false
      },

      {
        left: 633,
        width: 100,
        height: magicNumbers.PLATFORM_HEIGHT,
        fillStyle: 'rgb(80,140,230)',
        opacity: 1.0,
        track: 1,
        pulsate: false,
      },

      // Screen 2.......................................................

      {
        left: 810,
        width: 100,
        height: magicNumbers.PLATFORM_HEIGHT,
        fillStyle: 'rgb(200,200,0)',
        opacity: 1.0,
        track: 2,
        pulsate: false
      },

      {
        left: 1025,
        width: 100,
        height: magicNumbers.PLATFORM_HEIGHT,
        fillStyle: 'rgb(80,140,230)',
        opacity: 1.0,
        track: 2,
        pulsate: false
      },

      {
        left: 1200,
        width: 125,
        height: magicNumbers.PLATFORM_HEIGHT,
        fillStyle: 'aqua',
        opacity: 1.0,
        track: 3,
        pulsate: false
      },

      {
        left: 1400,
        width: 180,
        height: magicNumbers.PLATFORM_HEIGHT,
        fillStyle: 'rgb(80,140,230)',
        opacity: 1.0,
        track: 1,
        pulsate: false,
      },

      // Screen 3.......................................................

      {
        left: 1625,
        width: 100,
        height: magicNumbers.PLATFORM_HEIGHT,
        fillStyle: 'rgb(200,200,0)',
        opacity: 1.0,
        track: 2,
        pulsate: false
      },

      {
        left: 1800,
        width: 250,
        height: magicNumbers.PLATFORM_HEIGHT,
        fillStyle: 'rgb(80,140,230)',
        opacity: 1.0,
        track: 1,
           pulsate: false
      },

      {
        left: 2000,
        width: 100,
        height: magicNumbers.PLATFORM_HEIGHT,
        fillStyle: 'rgb(200,200,80)',
        opacity: 1.0,
        track: 2,
        pulsate: false
      },

      {
        left: 2100,
        width: 100,
        height: magicNumbers.PLATFORM_HEIGHT,
        fillStyle: 'aqua',
        opacity: 1.0,
        track: 3,
      },


      // Screen 4.......................................................

      {
        left: 2269,
        width: 200,
        height: magicNumbers.PLATFORM_HEIGHT,
        fillStyle: 'gold',
        opacity: 1.0,
        track: 1,
      },

      {
        left: 2500,
        width: 200,
        height: magicNumbers.PLATFORM_HEIGHT,
        fillStyle: '#2b950a',
        opacity: 1.0,
        track: 2,
        snail: true
      },
    ];

}