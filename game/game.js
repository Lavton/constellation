
/*
 * Copyright (C) 2012 David Geary. This code is from the book
 * Core HTML5 Canvas, published by Prentice-Hall in 2012.
 *
 * License:
 *
 * Permission is hereby granted, free of charge, to any person 
 * obtaining a copy of this software and associated documentation files
 * (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge,
 * publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * The Software may not be used to create training material of any sort,
 * including courses, books, instructional videos, presentations, etc.
 * without the express written consent of David Geary.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
*/
// snailBait constructor --------------------------------------------

var SnailBait =  function () {
   this.canvas = document.getElementById('game-canvas'),
   this.context = this.canvas.getContext('2d'),

   // HTML elements........................................................
   
   this.fpsElement = document.getElementById('fps'),
   this.toast = document.getElementById('toast'),

   // Constants............................................................

   this.LEFT = 1,
   this.RIGHT = 2,

   this.BAT_CELLS_HEIGHT = 34, // Bat cell width is not uniform
   this.BEE_CELLS_WIDTH  = 50,
   this.BEE_CELLS_HEIGHT = 50,
   this.BUTTON_CELLS_HEIGHT = 22,
   this.BUTTON_CELLS_WIDTH  = 30,
   this.COIN_CELLS_HEIGHT = 28,
   this.COIN_CELLS_WIDTH = 28,
   this.DEFAULT_TOAST_TIME = 3000, // 3 seconds
   this.EXPLOSION_CELLS_HEIGHT = 62,
   this.EXPLOSION_DURATION = 1000,
   this.RUBY_CELLS_HEIGHT  = 32,
   this.RUBY_CELLS_WIDTH = 32,
   this.SAPPHIRE_CELLS_HEIGHT = 32,
   this.SAPPHIRE_CELLS_WIDTH = 32,
   this.SNAIL_BOMB_CELLS_HEIGHT = 20,
   this.SNAIL_BOMB_CELLS_WIDTH = 20,
   this.SNAIL_CELLS_WIDTH = 64,
   this.SNAIL_CELLS_HEIGHT = 34,

   // Constants are listed in alphabetical order from here on out
   
   this.BACKGROUND_VELOCITY = 42,

   this.PAUSED_CHECK_INTERVAL = 200,

   this.PLATFORM_HEIGHT = 8,  
   this.PLATFORM_STROKE_WIDTH = 2,
   this.PLATFORM_STROKE_STYLE = 'rgb(0,0,0)',

   // Platform scrolling offset (and therefore speed) is
   // PLATFORM_VELOCITY_MULTIPLIER * backgroundOffset: The
   // platforms move PLATFORM_VELOCITY_MULTIPLIER times as
   // fast as the background.

   this.PLATFORM_VELOCITY_MULTIPLIER = 4.35,

   this.RUNNER_CELLS_HEIGHT = 60,
   this.RUNNER_PAGE_FLIP_INTERVAL = 48,
   this.RUNNER_HEIGHT = 43,
   
   this.STARTING_BACKGROUND_VELOCITY = 0,

   this.STARTING_BACKGROUND_OFFSET = 0,

   this.STARTING_RUNNER_LEFT = 50,
   this.STARTING_PAGEFLIP_INTERVAL = -1,
   this.STARTING_RUNNER_TRACK = 1,
   this.STARTING_RUNNER_VELOCITY = 0,

   // Paused............................................................
   
   this.paused = false,
   this.pauseStartTime = 0,
   this.totalTimePaused = 0,

   this.windowHasFocus = true,

   // Track baselines...................................................

   this.TRACK_1_BASELINE = 323,
   this.TRACK_2_BASELINE = 223,
   this.TRACK_3_BASELINE = 123,

   // Fps indicator.....................................................
   
   this.fpsToast = document.getElementById('fps'),

   // Images............................................................
   
   this.background  = new Image(),
   this.spritesheet = new Image(),

   // Time..............................................................
   
   this.lastAnimationFrameTime = 0,
   this.lastFpsUpdateTime = 0,
   this.fps = 60,

   // Pageflip timing for runner........................................

   this.runnerPageflipInterval = this.STARTING_PAGEFLIP_INTERVAL,
   
   // Translation offsets...............................................
   
   this.backgroundOffset = this.STARTING_BACKGROUND_OFFSET,
   this.spriteOffset = this.STARTING_BACKGROUND_OFFSET,

   // Velocities........................................................

   this.bgVelocity = this.STARTING_BACKGROUND_VELOCITY,
   this.platformVelocity,

   // Platforms.........................................................

   this.platformData = [
      // Screen 1.......................................................
      {
         left:      10,
         width:     230,
         height:    this.PLATFORM_HEIGHT,
         fillStyle: 'rgb(150,190,255)',
         opacity:   1.0,
         track:     1,
         pulsate:   false,
      },

      {  left:      250,
         width:     100,
         height:    this.PLATFORM_HEIGHT,
         fillStyle: 'rgb(150,190,255)',
         opacity:   1.0,
         track:     2,
         pulsate:   false,
      },

      {  left:      400,
         width:     125,
         height:    this.PLATFORM_HEIGHT,
         fillStyle: 'rgb(250,0,0)',
         opacity:   1.0,
         track:     3,
         pulsate:   false
      },

      {  left:      633,
         width:     100,
         height:    this.PLATFORM_HEIGHT,
         fillStyle: 'rgb(80,140,230)',
         opacity:   1.0,
         track:     1,
         pulsate:   false,
      },

      // Screen 2.......................................................
               
      {  left:      810,
         width:     100,
         height:    this.PLATFORM_HEIGHT,
         fillStyle: 'rgb(200,200,0)',
         opacity:   1.0,
         track:     2,
         pulsate:   false
      },

      {  left:      1025,
         width:     100,
         height:    this.PLATFORM_HEIGHT,
         fillStyle: 'rgb(80,140,230)',
         opacity:   1.0,
         track:     2,
         pulsate:   false
      },

      {  left:      1200,
         width:     125,
         height:    this.PLATFORM_HEIGHT,
         fillStyle: 'aqua',
         opacity:   1.0,
         track:     3,
         pulsate:   false
      },

      {  left:      1400,
         width:     180,
         height:    this.PLATFORM_HEIGHT,
         fillStyle: 'rgb(80,140,230)',
         opacity:   1.0,
         track:     1,
         pulsate:   false,
      },

      // Screen 3.......................................................
               
      {  left:      1625,
         width:     100,
         height:    this.PLATFORM_HEIGHT,
         fillStyle: 'rgb(200,200,0)',
         opacity:   1.0,
         track:     2,
         pulsate:   false
      },

      {  left:      1800,
         width:     250,
         height:    this.PLATFORM_HEIGHT,
         fillStyle: 'rgb(80,140,230)',
         opacity:   1.0,
         track:     1,
         pulsate:   false
      },

      {  left:      2000,
         width:     100,
         height:    this.PLATFORM_HEIGHT,
         fillStyle: 'rgb(200,200,80)',
         opacity:   1.0,
         track:     2,
         pulsate:   false
      },

      {  left:      2100,
         width:     100,
         height:    this.PLATFORM_HEIGHT,
         fillStyle: 'aqua',
         opacity:   1.0,
         track:     3,
      },


      // Screen 4.......................................................

      {  left:      2269,
         width:     200,
         height:    this.PLATFORM_HEIGHT,
         fillStyle: 'gold',
         opacity:   1.0,
         track:     1,
      },

      {  left:      2500,
         width:     200,
         height:    this.PLATFORM_HEIGHT,
         fillStyle: '#2b950a',
         opacity:   1.0,
         track:     2,
         snail:     true
      },
   ],

   // Бегущая девочка.........................................................

   this.runnerCellsRight = [
      { left: 11, top: 9, width: 41, height: this.RUNNER_CELLS_HEIGHT },
      { left: 58, top: 15, width: 47, height: this.RUNNER_CELLS_HEIGHT },
      { left: 102, top: 13, width: 45, height: this.RUNNER_CELLS_HEIGHT },
      { left: 148, top: 12, width: 43, height: this.RUNNER_CELLS_HEIGHT },
      { left: 194, top: 10, width: 44, height: this.RUNNER_CELLS_HEIGHT },
      { left: 238, top: 13, width: 48, height: this.RUNNER_CELLS_HEIGHT },
      { left: 289,  top: 14, width: 40, height: this.RUNNER_CELLS_HEIGHT },
      { left: 330,  top: 12, width: 44, height: this.RUNNER_CELLS_HEIGHT }
   ],

   this.runnerCellsLeft = [
      { left: 7,   top: 86, width: 44, height: this.RUNNER_CELLS_HEIGHT },
      { left: 52,  top: 87, width: 40, height: this.RUNNER_CELLS_HEIGHT },
      { left: 96, top: 86, width: 48, height: this.RUNNER_CELLS_HEIGHT },
      { left: 141, top: 87, width: 44, height: this.RUNNER_CELLS_HEIGHT },
      { left: 192, top: 87, width: 43, height: this.RUNNER_CELLS_HEIGHT },
      { left: 236, top: 87, width: 45, height: this.RUNNER_CELLS_HEIGHT },
      { left: 281, top: 87, width: 47, height: this.RUNNER_CELLS_HEIGHT },
      { left: 328, top: 87, width: 41, height: this.RUNNER_CELLS_HEIGHT },
   ],


   // Бегущий парень.........................................................

   this.runnerManCellsRight = [
      { left: 20, top: 165, width: 46, height: this.RUNNER_CELLS_HEIGHT },
      { left: 76, top: 165, width: 52, height: this.RUNNER_CELLS_HEIGHT },
      { left: 128, top: 165, width: 62, height: this.RUNNER_CELLS_HEIGHT },
      { left: 198, top: 165, width: 57, height: this.RUNNER_CELLS_HEIGHT },
      { left: 264, top: 165, width: 50, height: this.RUNNER_CELLS_HEIGHT },
      { left: 322, top: 165, width: 54, height: this.RUNNER_CELLS_HEIGHT },
      { left: 181,  top: 165, width: 58, height: this.RUNNER_CELLS_HEIGHT },
      { left: 151,  top: 165, width: 55, height: this.RUNNER_CELLS_HEIGHT }
   ],

   this.runnerManCellsLeft = [
      { left: 4,   top: 245, width: 55, height: this.RUNNER_CELLS_HEIGHT },
      { left: 67,  top: 245, width: 58, height: this.RUNNER_CELLS_HEIGHT },
      { left: 133, top: 245, width: 54, height: this.RUNNER_CELLS_HEIGHT },
      { left: 196, top: 245, width: 50, height: this.RUNNER_CELLS_HEIGHT },
      { left: 255, top: 245, width: 57, height: this.RUNNER_CELLS_HEIGHT },
      { left: 316, top: 245, width: 62, height: this.RUNNER_CELLS_HEIGHT },
      { left: 376, top: 245, width: 52, height: this.RUNNER_CELLS_HEIGHT },
      { left: 445, top: 245, width: 46, height: this.RUNNER_CELLS_HEIGHT },
   ],
   // Bats..............................................................
   
   this.batData = [
      { left: 1150, top: this.TRACK_2_BASELINE - this.BAT_CELLS_HEIGHT },
      { left: 1720, top: this.TRACK_2_BASELINE - 2*this.BAT_CELLS_HEIGHT },
      { left: 2000, top: this.TRACK_3_BASELINE }, 
      { left: 2200, top: this.TRACK_3_BASELINE - this.BAT_CELLS_HEIGHT },
      { left: 2400, top: this.TRACK_3_BASELINE - 2*this.BAT_CELLS_HEIGHT },
   ],
   
   // Bees..............................................................

   this.beeData = [
      { left: 500,  top: 64 },
      { left: 944,  top: this.TRACK_2_BASELINE - this.BEE_CELLS_HEIGHT - 30 },
      { left: 1600, top: 125 },
      { left: 2225, top: 125 },
      { left: 2295, top: 275 },
      { left: 2450, top: 275 },
   ],
   
   // Buttons...........................................................

   this.buttonData = [
      { platformIndex: 7 },
      { platformIndex: 12 },
   ],

   // Coins.............................................................

   this.coinData = [
      { left: 303,  top: this.TRACK_2_BASELINE - this.COIN_CELLS_HEIGHT }, 
      { left: 469,  top: this.TRACK_3_BASELINE - 2*this.COIN_CELLS_HEIGHT }, 
      { left: 600,  top: this.TRACK_1_BASELINE - this.COIN_CELLS_HEIGHT }, 
      { left: 833,  top: this.TRACK_2_BASELINE - 2*this.COIN_CELLS_HEIGHT }, 
      { left: 1050, top: this.TRACK_2_BASELINE - 2*this.COIN_CELLS_HEIGHT }, 
      { left: 1500, top: this.TRACK_1_BASELINE - 2*this.COIN_CELLS_HEIGHT }, 
      { left: 1670, top: this.TRACK_2_BASELINE - 2*this.COIN_CELLS_HEIGHT }, 
      { left: 1870, top: this.TRACK_1_BASELINE - 2*this.COIN_CELLS_HEIGHT }, 
      { left: 1930, top: this.TRACK_1_BASELINE - 2*this.COIN_CELLS_HEIGHT }, 
      { left: 2200, top: this.TRACK_3_BASELINE - 3*this.COIN_CELLS_HEIGHT }, 
   ],

   // Rubies............................................................

   this.rubyData = [
      { left: 200,  top: this.TRACK_1_BASELINE - this.RUBY_CELLS_HEIGHT },
      { left: 880,  top: this.TRACK_2_BASELINE - this.RUBY_CELLS_HEIGHT },
      { left: 1100, top: this.TRACK_2_BASELINE - 2*this.SAPPHIRE_CELLS_HEIGHT }, 
      { left: 1475, top: this.TRACK_1_BASELINE - this.RUBY_CELLS_HEIGHT },
   ],

   // Sapphires.........................................................

   this.sapphireData = [
      { left: 680,  top: this.TRACK_1_BASELINE - this.SAPPHIRE_CELLS_HEIGHT },
      { left: 1700, top: this.TRACK_2_BASELINE - this.SAPPHIRE_CELLS_HEIGHT },
      { left: 2056, top: this.TRACK_2_BASELINE - 3*this.SAPPHIRE_CELLS_HEIGHT/2 },
      { left: 2333, top: this.TRACK_2_BASELINE - this.SAPPHIRE_CELLS_HEIGHT },
   ],

   // Snails............................................................

   this.snailData = [
      { platformIndex: 13 },
   ],
   
   // Snail bomb........................................................

   this.snailBombData = [
      { left: 2600, top: this.TRACK_2_BASELINE - this.SNAIL_BOMB_CELLS_HEIGHT }
   ],
   
   // Spritesheet cells................................................

   this.batCells = [
      { left: 1,   top: 0, width: 32, height: this.BAT_CELLS_HEIGHT },
      { left: 38,  top: 0, width: 46, height: this.BAT_CELLS_HEIGHT },
      { left: 90,  top: 0, width: 32, height: this.BAT_CELLS_HEIGHT },
      { left: 129, top: 0, width: 46, height: this.BAT_CELLS_HEIGHT },
   ],

   this.batRedEyeCells = [
      { left: 185, top: 0, width: 32, height: this.BAT_CELLS_HEIGHT },
      { left: 222, top: 0, width: 46, height: this.BAT_CELLS_HEIGHT },
      { left: 273, top: 0, width: 32, height: this.BAT_CELLS_HEIGHT },
      { left: 313, top: 0, width: 46, height: this.BAT_CELLS_HEIGHT },
   ],
   
   this.beeCells = [
      { left: 5,   top: 234, width: this.BEE_CELLS_WIDTH,
                            height: this.BEE_CELLS_HEIGHT },

      { left: 75,  top: 234, width: this.BEE_CELLS_WIDTH, 
                            height: this.BEE_CELLS_HEIGHT },

      { left: 145, top: 234, width: this.BEE_CELLS_WIDTH, 
                            height: this.BEE_CELLS_HEIGHT }
   ],
   
   this.buttonCells = [
      { left: 2,   top: 190, width: this.BUTTON_CELLS_WIDTH,
                            height: this.BUTTON_CELLS_HEIGHT },

      { left: 45,  top: 190, width: this.BUTTON_CELLS_WIDTH, 
                            height: this.BUTTON_CELLS_HEIGHT }
   ],

   this.coinCells = [
      { left: 2, top: 540, width: this.COIN_CELLS_WIDTH, height: this.COIN_CELLS_HEIGHT }
   ],

   this.explosionCells = [
      { left: 1,   top: 48, width: 50, height: this.EXPLOSION_CELLS_HEIGHT },
      { left: 60,  top: 48, width: 68, height: this.EXPLOSION_CELLS_HEIGHT },
      { left: 143, top: 48, width: 68, height: this.EXPLOSION_CELLS_HEIGHT },
      { left: 230, top: 48, width: 68, height: this.EXPLOSION_CELLS_HEIGHT },
      { left: 305, top: 48, width: 68, height: this.EXPLOSION_CELLS_HEIGHT },
      { left: 389, top: 48, width: 68, height: this.EXPLOSION_CELLS_HEIGHT },
      { left: 470, top: 48, width: 68, height: this.EXPLOSION_CELLS_HEIGHT }
   ],

   this.goldButtonCells = [
      { left: 88,   top: 190, width: this.BUTTON_CELLS_WIDTH,
                              height: this.BUTTON_CELLS_HEIGHT },

      { left: 130,  top: 190, width: this.BUTTON_CELLS_WIDTH,
                              height: this.BUTTON_CELLS_HEIGHT }
   ],

   this.rubyCells = [
      { left: 3,   top: 135, width: this.RUBY_CELLS_WIDTH,
                             height: this.RUBY_CELLS_HEIGHT },

      { left: 39,  top: 135, width: this.RUBY_CELLS_WIDTH, 
                             height: this.RUBY_CELLS_HEIGHT },

      { left: 76,  top: 135, width: this.RUBY_CELLS_WIDTH, 
                             height: this.RUBY_CELLS_HEIGHT },

      { left: 112, top: 135, width: this.RUBY_CELLS_WIDTH, 
                             height: this.RUBY_CELLS_HEIGHT },

      { left: 148, top: 135, width: this.RUBY_CELLS_WIDTH, 
                             height: this.RUBY_CELLS_HEIGHT }
   ],

   this.sapphireCells = [
      { left: 185,   top: 135, width: this.SAPPHIRE_CELLS_WIDTH,
                               height: this.SAPPHIRE_CELLS_HEIGHT },

      { left: 220,  top: 135, width: this.SAPPHIRE_CELLS_WIDTH, 
                               height: this.SAPPHIRE_CELLS_HEIGHT },

      { left: 258,  top: 135, width: this.SAPPHIRE_CELLS_WIDTH, 
                               height: this.SAPPHIRE_CELLS_HEIGHT },

      { left: 294, top: 135, width: this.SAPPHIRE_CELLS_WIDTH, 
                               height: this.SAPPHIRE_CELLS_HEIGHT },

      { left: 331, top: 135, width: this.SAPPHIRE_CELLS_WIDTH, 
                               height: this.SAPPHIRE_CELLS_HEIGHT }
   ],

   this.snailBombCells = [
      { left: 2, top: 512, width: 30, height: 20 }
   ],

   this.snailCells = [
      { left: 142, top: 466, width: this.SNAIL_CELLS_WIDTH,
                             height: this.SNAIL_CELLS_HEIGHT },

      { left: 75,  top: 466, width: this.SNAIL_CELLS_WIDTH, 
                             height: this.SNAIL_CELLS_HEIGHT },

      { left: 2,   top: 466, width: this.SNAIL_CELLS_WIDTH, 
                             height: this.SNAIL_CELLS_HEIGHT },
   ],

   // Sprites...........................................................
  
   this.bats         = [],
   this.bees         = [], 
   this.buttons      = [],
   this.coins        = [],
   this.platforms    = [],
   this.rubies       = [],
   this.sapphires    = [],
   this.snails       = [],
   this.snailBombs   = [], 
   
   // Sprite artists...................................................

   this.runnerArtist = new SpriteSheetArtist(this.spritesheet, this.runnerCellsRight),

   this.platformArtist = {
      draw: function (sprite, context) {
         var top;
         
         context.save();

         top = snailBait.calculatePlatformTop(sprite.track);

         context.lineWidth = snailBait.PLATFORM_STROKE_WIDTH;
         context.strokeStyle = snailBait.PLATFORM_STROKE_STYLE;
         context.fillStyle = sprite.fillStyle;

         context.strokeRect(sprite.left, top, sprite.width, sprite.height);
         context.fillRect  (sprite.left, top, sprite.width, sprite.height);

         context.restore();
      }
   },

   // Sprites...........................................................

   this.runner = new Sprite('runner', this.runnerArtist);

   // All sprites.......................................................
   // 
   // (addSpritesToSpriteArray() adds sprites from the preceding sprite
   // arrays to the sprites array)

   this.sprites = [ this.runner ];  

   this.explosionAnimator = new SpriteAnimator(
      this.explosionCells,          // Animation cells
      this.EXPLOSION_DURATION,      // Duration of the explosion
      function (sprite, animator) { // Callback after animation
         sprite.exploding = false; 
      }
   );
};


// snailBait.prototype ----------------------------------------------


SnailBait.prototype = {
   // Drawing..............................................................

   draw: function (now) {
      this.setPlatformVelocity();
      this.setTranslationOffsets();

      this.drawBackground();

      this.updateSprites(now);
      this.drawSprites();
   },

   setPlatformVelocity: function () {
      this.platformVelocity = this.bgVelocity * this.PLATFORM_VELOCITY_MULTIPLIER; 
   },

   setTranslationOffsets: function () {
      this.setBackgroundTranslationOffset();
      this.setSpriteTranslationOffsets();
   },
   
   setSpriteTranslationOffsets: function () {
      var i, sprite;
   
      this.spriteOffset += this.platformVelocity / this.fps; // In step with platforms

      for (i=0; i < this.sprites.length; ++i) {
         sprite = this.sprites[i];
      
         if ('runner' !== sprite.type) {
            sprite.offset = this.spriteOffset; 
         }
      }
   },

   setBackgroundTranslationOffset: function () {
      var offset = this.backgroundOffset + this.bgVelocity/this.fps;
   
      if (offset > 0 && offset < this.background.width) {
         this.backgroundOffset = offset;
      }
      else {
         this.backgroundOffset = 0;
      }
   },
   
   drawBackground: function () {
      this.context.save();
   
      this.context.globalAlpha = 1.0;
      this.context.translate(-this.backgroundOffset, 0);
   
      // Initially onscreen:
      this.context.drawImage(this.background, 0, 0,
                        this.background.width, this.background.height);
   
      // Initially offscreen:
      this.context.drawImage(this.background, this.background.width, 0,
                        this.background.width+1, this.background.height);
   
      this.context.restore();
   },
   
   calculateFps: function (now) {
      var fps = 1000 / (now - this.lastAnimationFrameTime);
      this.lastAnimationFrameTime = now;
   
      if (now - this.lastFpsUpdateTime > 1000) {
         this.lastFpsUpdateTime = now;
         this.fpsElement.innerHTML = fps.toFixed(0) + ' fps';
      }

      return fps; 
   },
   
   calculatePlatformTop: function (track) {
      var top;
   
      if      (track === 1) { top = this.TRACK_1_BASELINE; }
      else if (track === 2) { top = this.TRACK_2_BASELINE; }
      else if (track === 3) { top = this.TRACK_3_BASELINE; }

      return top;
   },

   turnLeft: function () {
      this.bgVelocity = -this.BACKGROUND_VELOCITY;
      this.runnerPageflipInterval = this.RUNNER_PAGE_FLIP_INTERVAL;
      this.runnerArtist.cells = this.runnerCellsLeft;
      this.runner.direction = this.LEFT;
   },

   turnRight: function () {
      this.bgVelocity = this.BACKGROUND_VELOCITY;
      this.runnerPageflipInterval = this.RUNNER_PAGE_FLIP_INTERVAL;
      this.runnerArtist.cells = this.runnerCellsRight;
      this.runner.direction = this.RIGHT;
   },
   
   // Sprites..............................................................
 
   explode: function (sprite, silent) {
      sprite.exploding = true;
      this.explosionAnimator.start(sprite, true);  // true means sprite reappears
   },

   equipRunner: function () {
      this.runner.track = this.STARTING_RUNNER_TRACK;
      this.runner.direction = this.LEFT;
      this.runner.velocityX = this.STARTING_RUNNER_VELOCITY;

      this.runner.left = this.STARTING_RUNNER_LEFT;
      this.runner.top = this.calculatePlatformTop(this.runner.track) - this.RUNNER_CELLS_HEIGHT;

      this.runner.artist.cells = this.runnerCellsRight;
   },

   createPlatformSprites: function () {
      var sprite, pd;  // Sprite, Platform data
   
      for (var i=0; i < this.platformData.length; ++i) {
         pd = this.platformData[i];
         sprite  = new Sprite('platform-' + i, this.platformArtist);

         sprite.left      = pd.left;
         sprite.width     = pd.width;
         sprite.height    = pd.height;
         sprite.fillStyle = pd.fillStyle;
         sprite.opacity   = pd.opacity;
         sprite.track     = pd.track;
         sprite.button    = pd.button;
         sprite.pulsate   = pd.pulsate;

         sprite.top = this.calculatePlatformTop(pd.track);
   
         this.platforms.push(sprite);
      }
   },

   // Animation............................................................

   animate: function (now) { 
      if (snailBait.paused) {
         setTimeout( function () {
            requestNextAnimationFrame(snailBait.animate);
         }, snailBait.PAUSED_CHECK_INTERVAL);
      }
      else {
         snailBait.fps = snailBait.calculateFps(now); 
         snailBait.draw(now);
         requestNextAnimationFrame(snailBait.animate);
      }
   },

   togglePaused: function () {
      var now = +new Date();

      this.paused = !this.paused;
   
      if (this.paused) {
         this.pauseStartTime = now;
      }
      else {
         this.lastAnimationFrameTime += (now - this.pauseStartTime);
      }
   },

   // ------------------------- INITIALIZATION ----------------------------

   start: function () {
      this.createSprites();
      this.initializeImages();
      this.equipRunner();
      this.splashToast('Good Luck!');
   },
   
   initializeImages: function () {
      var self = this;

      this.background.src = 'images/mb.jpg';
      this.spritesheet.src = 'images/sprites.png';
   
      this.background.onload = function (e) {
         self.startGame();
      };
   },

   startGame: function () {
      requestNextAnimationFrame(this.animate);
   },

   positionSprites: function (sprites, spriteData) {
      var sprite;

      for (var i = 0; i < sprites.length; ++i) {
         sprite = sprites[i];

         if (spriteData[i].platformIndex) { // no platform
            this.putSpriteOnPlatform(sprite, this.platforms[spriteData[i].platformIndex]);
         }
         else {
            sprite.top  = spriteData[i].top;
            sprite.left = spriteData[i].left;
         }
      }
   },

   addSpritesToSpriteArray: function () {
      for (var i=0; i < this.platforms.length; ++i) {
         this.sprites.push(this.platforms[i]);
      }

      for (var i=0; i < this.bats.length; ++i) {
         this.sprites.push(this.bats[i]);
      }

      for (var i=0; i < this.bees.length; ++i) {
         this.sprites.push(this.bees[i]);
      }

      for (var i=0; i < this.buttons.length; ++i) {
         this.sprites.push(this.buttons[i]);
      }

      for (var i=0; i < this.coins.length; ++i) {
         this.sprites.push(this.coins[i]);
      }

      for (var i=0; i < this.rubies.length; ++i) {
         this.sprites.push(this.rubies[i]);
      }

      for (var i=0; i < this.sapphires.length; ++i) {
         this.sprites.push(this.sapphires[i]);
      }

     for (var i=0; i < this.snails.length; ++i) {
         this.sprites.push(this.snails[i]);
      }

      for (var i=0; i < this.snailBombs.length; ++i) {
         this.sprites.push(this.snailBombs[i]);
      }
   },

   createBatSprites: function () {
      var bat,
          batArtist = new SpriteSheetArtist(this.spritesheet, this.batCells),
    redEyeBatArtist = new SpriteSheetArtist(this.spritesheet, this.batRedEyeCells);

      for (var i = 0; i < this.batData.length; ++i) {
         if (i % 2 === 0) bat = new Sprite('bat', batArtist);
         else             bat = new Sprite('bat', redEyeBatArtist);

         bat.width = this.BAT_CELLS_WIDTH;
         bat.height = this.BAT_CELLS_HEIGHT;

         this.bats.push(bat);
      }
   },

   createBeeSprites: function () {
      var bee,
          beeArtist = new SpriteSheetArtist(this.spritesheet, this.beeCells);

      for (var i = 0; i < this.beeData.length; ++i) {
         bee = new Sprite('bee', beeArtist);

         bee.width = this.BEE_CELLS_WIDTH;
         bee.height = this.BEE_CELLS_HEIGHT;

         this.bees.push(bee);
      }
   },

   createButtonSprites: function () {
      var button,
          buttonArtist = new SpriteSheetArtist(this.spritesheet,
                                               this.buttonCells),
      goldButtonArtist = new SpriteSheetArtist(this.spritesheet,
                                               this.goldButtonCells);
   
      for (var i = 0; i < this.buttonData.length; ++i) {
         if (i === this.buttonData.length - 1) {
            button = new Sprite('button', goldButtonArtist);
         }
         else {
            button = new Sprite('button', buttonArtist);
         }

         button.width = this.BUTTON_CELLS_WIDTH;
         button.height = this.BUTTON_CELLS_HEIGHT;

         this.buttons.push(button);
      }
   },
   
   createCoinSprites: function () {
      var coin,
          coinArtist = new SpriteSheetArtist(this.spritesheet, this.coinCells);
   
      for (var i = 0; i < this.coinData.length; ++i) {
         coin = new Sprite('coin', coinArtist);

         coin.width = this.COIN_CELLS_WIDTH;
         coin.height = this.COIN_CELLS_HEIGHT;

         this.coins.push(coin);
      }
   },
   
   createSapphireSprites: function () {
      var sapphire,
          sapphireArtist = new SpriteSheetArtist(this.spritesheet, this.sapphireCells);
   
      for (var i = 0; i < this.sapphireData.length; ++i) {
         sapphire = new Sprite('sapphire', sapphireArtist);

         sapphire.width = this.SAPPHIRE_CELLS_WIDTH;
         sapphire.height = this.SAPPHIRE_CELLS_HEIGHT;

         this.sapphires.push(sapphire);
      }
   },
   
   createRubySprites: function () {
      var ruby,
          rubyArtist = new SpriteSheetArtist(this.spritesheet, this.rubyCells);
   
      for (var i = 0; i < this.rubyData.length; ++i) {
         ruby = new Sprite('ruby', rubyArtist);

         ruby.width = this.RUBY_CELLS_WIDTH;
         ruby.height = this.RUBY_CELLS_HEIGHT;
         
         this.rubies.push(ruby);
      }
   },
   
   createSnailSprites: function () {
      var snail,
          snailArtist = new SpriteSheetArtist(this.spritesheet,
                                              this.rubyCells),
          snailBombArtist = new SpriteSheetArtist(this.spritesheet,
                                                  this.snailBombCells);
   
      for (var i = 0; i < this.snailData.length; ++i) {
         snail = new Sprite('snail', snailArtist);

         snail.width = this.SNAIL_CELLS_WIDTH;
         snail.height = this.SNAIL_CELLS_HEIGHT;

         snail.bomb = new Sprite('snailBomb', snailBombArtist);

         snail.bomb.width = this.SNAILBOMB_CELLS_WIDTH;
         snail.bomb.height = this.SNAILBOMB_CELLS_HEIGHT;

         snail.bomb.snail = snail;  // Snail bombs maintain a reference to their snail
         
         this.snails.push(snail);
         this.snailBombs.push(snail.bomb);
      }
   },
   
   updateSprites: function (now) {
      var sprite;
   
      for (var i=0; i < this.sprites.length; ++i) {
         sprite = this.sprites[i];
         if (sprite.visible && this.spriteInView(sprite)) {
            sprite.update(this.context, now, this.fps);
         }
      }
   },
   
   drawSprites: function() {
      var sprite;
   
      for (var i=0; i < this.sprites.length; ++i) {
         sprite = this.sprites[i];
   
         if (sprite.visible && this.spriteInView(sprite)) {
            this.context.translate(-sprite.offset, 0);

            sprite.draw(this.context);

            this.context.translate(sprite.offset, 0);
         }
      }
   },
   
   spriteInView: function(sprite) {
      return sprite === this.runner || // runner is always visible
         (sprite.left + sprite.width > this.spriteOffset &&
          sprite.left < this.spriteOffset + this.canvas.width);   
   },
   
   putSpriteOnPlatform: function(sprite, platformSprite) {
      sprite.top  = platformSprite.top - sprite.height;
      sprite.left = platformSprite.left;
      sprite.platform = platformSprite;
   },
   
   createSprites: function() {  
      this.createPlatformSprites(); // Platforms must be created first
      
      this.createBatSprites();
      this.createBeeSprites();
      this.createButtonSprites();
      this.createCoinSprites();
      this.createRubySprites();
      this.createSapphireSprites();
      this.createSnailSprites();

      this.initializeSprites();

      this.addSpritesToSpriteArray();
   },
   
   initializeSprites: function() {  
      this.positionSprites(this.bats,       this.batData);
      this.positionSprites(this.bees,       this.beeData);
      this.positionSprites(this.buttons,    this.buttonData);
      this.positionSprites(this.coins,      this.coinData);
      this.positionSprites(this.rubies,     this.rubyData);
      this.positionSprites(this.sapphires,  this.sapphireData);
      this.positionSprites(this.snails,     this.snailData);
      this.positionSprites(this.snailBombs, this.snailBombData);
   },

   // Toast................................................................

   splashToast: function (text, howLong) {
      howLong = howLong || this.DEFAULT_TOAST_TIME;

      toast.style.display = 'block';
      toast.innerHTML = text;

      setTimeout( function (e) {
         if (snailBait.windowHasFocus) {
            toast.style.opacity = 1.0; // After toast is displayed
         }
      }, 50);

      setTimeout( function (e) {
         if (snailBait.windowHasFocus) {
            toast.style.opacity = 0; // Starts CSS3 transition
         }

         setTimeout( function (e) { 
            if (snailBait.windowHasFocus) {
               toast.style.display = 'none'; 
            }
         }, 480);
      }, howLong);
   },
};
   
// Event handlers.......................................................
   
window.onkeydown = function (e) {
   var key = e.keyCode;

   if (key === 80 || (snailBait.paused && key !== 80)) {  // 'p'
      snailBait.togglePaused();
   }
   
   if (key === 68 || key === 37) { // 'd' or left arrow
      snailBait.turnLeft();
   }
   else if (key === 75 || key === 39) { // 'k'or right arrow
      snailBait.turnRight();
   }
   else if (key === 74) { // 'j'
      if (snailBait.runner.track === 3) {
         return;
      }
      snailBait.runner.track++;
   }
   else if (key === 70) { // 'f'
      if (snailBait.runner.track === 1) {
         return;
      }

      snailBait.runner.track--;
   }

   snailBait.runner.top = snailBait.calculatePlatformTop(snailBait.runner.track) -
                      snailBait.RUNNER_CELLS_HEIGHT;
};

window.onblur = function (e) {  // pause if unpaused
   snailBait.windowHasFocus = false;
   
   if (!snailBait.paused) {
      snailBait.togglePaused();
   }
};

window.onfocus = function (e) {  // unpause if paused
   var originalFont = snailBait.toast.style.fontSize;

   snailBait.windowHasFocus = true;

   if (snailBait.paused) {
      snailBait.toast.style.font = '128px fantasy';

      snailBait.splashToast('3', 500); // Display 3 for one half second

      setTimeout(function (e) {
         snailBait.splashToast('2', 500); // Display 2 for one half second

         setTimeout(function (e) {
            snailBait.splashToast('1', 500); // Display 1 for one half second

            setTimeout(function (e) {
               if ( snailBait.windowHasFocus) {
                  snailBait.togglePaused();
               }

               setTimeout(function (e) { // Wait for '1' to disappear
                  snailBait.toast.style.fontSize = originalFont;
               }, 2000);
            }, 1000);
         }, 1000);
      }, 1000);
   }
};

// Launch game.........................................................

var snailBait = new SnailBait();
snailBait.start();
