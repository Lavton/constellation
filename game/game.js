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
// constellationGame constructor --------------------------------------------

var ConstellationGame = function() {
  // получаем данные из других файлов
  window.newself = this;
  _.forEach(new cellsFactory(), function(el, ind) {
    window.newself[ind] = el;
  })
  _.forEach(magicNumbers, function(el, ind) {
    window.newself[ind] = el;
  })
  _.forEach(new platformFactory(), function(el, ind) {
    window.newself[ind] = el;
  })
  _.forEach(new dataFactory(), function(el, ind) {
    window.newself[ind] = el;
  })


  this.canvas = document.getElementById('game-canvas'),
    this.context = this.canvas.getContext('2d'),

    // HTML elements........................................................

    this.fpsElement = document.getElementById('fps'),
    this.toast = document.getElementById('toast'),
    this.manOrWomanElement = document.getElementById('man-or-woman');
  // Constants............................................................

  this.LEFT = 1,
    this.RIGHT = 2,

    this.BAT_CELLS_HEIGHT = 34, // Bat cell width is not uniform
    this.BEE_CELLS_WIDTH = 50,
    this.BEE_CELLS_HEIGHT = 50,
    this.BUTTON_CELLS_HEIGHT = 22,
    this.BUTTON_CELLS_WIDTH = 30,
    this.COIN_CELLS_HEIGHT = 28,
    this.COIN_CELLS_WIDTH = 28,
    this.DEFAULT_TOAST_TIME = 3000, // 3 seconds
    this.EXPLOSION_CELLS_HEIGHT = 62,
    this.EXPLOSION_DURATION = 1000,
    this.RUBY_CELLS_HEIGHT = 32,
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

    this.PLATFORM_STROKE_WIDTH = 2,
    this.PLATFORM_STROKE_STYLE = 'rgb(0,0,0)',

    // Platform scrolling offset (and therefore speed) is
    // PLATFORM_VELOCITY_MULTIPLIER * backgroundOffset: The
    // platforms move PLATFORM_VELOCITY_MULTIPLIER times as
    // fast as the background.

    this.PLATFORM_VELOCITY_MULTIPLIER = 4.35,

    // this.RUNNER_CELLS_HEIGHT = 60,
    // this.RUNNER_CELLS_HEIGHT = magicNumbers.RUNNER_CELLS_HEIGHT,
    this.RUNNER_PAGE_FLIP_INTERVAL = 48,
    this.RUNNER_HEIGHT = 43,

    this.STARTING_BACKGROUND_VELOCITY = 0,

    this.STARTING_BACKGROUND_OFFSET = 0,

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

    this.keyPress = magicNumbers.now_going.NOWHERE,

    // Fps indicator.....................................................

    this.fpsToast = document.getElementById('fps'),

    // Images............................................................

    this.background = new Image(),
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

    // Sprites...........................................................
    this.orangeStars = [],
    this.clouds = [],
    this.redStars = [],
    this.platforms = [],
    this.oldMans = [],

    // Sprite artists...................................................

    this.runnerArtist = new SpriteSheetArtist(this.spritesheet, this.runnerCellsRight),

    this.platformArtist = {
      draw: function(sprite, context) {
        var top;

        context.save();

        top = constellationGame.calculatePlatformTop(sprite.track);

        context.lineWidth = constellationGame.PLATFORM_STROKE_WIDTH;
        context.strokeStyle = constellationGame.PLATFORM_STROKE_STYLE;
        context.fillStyle = sprite.fillStyle;

        context.strokeRect(sprite.left, top, sprite.width, sprite.height);
        context.fillRect(sprite.left, top, sprite.width, sprite.height);

        context.restore();
      }
    },

    // Sprites...........................................................

    this.runner = new Sprite('runner', this.runnerArtist, [
      new Run(),
      new runnerShoot(),
      new Jump(),
      new Collide(),
    ]);
  this.runner.common_type == "runner"
  this.runner.height = magicNumbers.RUNNER_CELLS_HEIGHT;
  this.runner.fall = function() {
    constellationGame.runner.track = 1;
    constellationGame.runner.top = constellationGame.calculatePlatformTop(constellationGame.runner.track) - constellationGame.runner.height;
  };

  // All sprites.......................................................
  // 
  // (addSpritesToSpriteArray() adds sprites from the preceding sprite
  // arrays to the sprites array)

  this.sprites = [this.runner];

  this.explosionAnimator = new SpriteAnimator(
    this.explosionCells, // Animation cells
    this.EXPLOSION_DURATION, // Duration of the explosion
    function(sprite, animator) { // Callback after animation
      sprite.exploding = false;
    }
  );
};


// constellationGame.prototype ----------------------------------------------


ConstellationGame.prototype = {
  // Drawing..............................................................

  draw: function(now) {
    this.setPlatformVelocity();
    this.setTranslationOffsets();

    this.drawBackground();

    this.updateSprites(now);
    this.drawSprites();
  },

  setPlatformVelocity: function() {
    this.platformVelocity = this.bgVelocity * this.PLATFORM_VELOCITY_MULTIPLIER;
  },

  setTranslationOffsets: function() {
    this.setBackgroundTranslationOffset();
    this.setSpriteTranslationOffsets();
  },

  setSpriteTranslationOffsets: function() {
    var i, sprite;

    this.spriteOffset += this.platformVelocity / this.fps; // In step with platforms

    for (i = 0; i < this.sprites.length; ++i) {
      sprite = this.sprites[i];

      if ('runner' !== sprite.type) {
        sprite.offset = this.spriteOffset;
      }
    }
  },

  setBackgroundTranslationOffset: function() {
    var offset = this.backgroundOffset + this.bgVelocity / this.fps;

    if (offset > 0 && offset < this.background.width) {
      this.backgroundOffset = offset;
    } else {
      this.backgroundOffset = 0;
    }
  },

  drawBackground: function() {
    this.context.save();

    this.context.globalAlpha = 1.0;
    this.context.translate(-this.backgroundOffset, 0);

    // Initially onscreen:
    this.context.drawImage(this.background, 0, 0,
      this.background.width, this.background.height);

    // Initially offscreen:
    this.context.drawImage(this.background, this.background.width, 0,
      this.background.width + 1, this.background.height);

    this.context.restore();
  },

  calculateFps: function(now) {
    var fps = 1000 / (now - this.lastAnimationFrameTime);
    this.lastAnimationFrameTime = now;

    if (now - this.lastFpsUpdateTime > 1000) {
      this.lastFpsUpdateTime = now;
      this.fpsElement.innerHTML = fps.toFixed(0) + ' fps';
    }

    return fps;
  },

  calculatePlatformTop: function(track) {
    var top;

    if (track === 1) {
      top = this.TRACK_1_BASELINE;
    } else if (track === 2) {
      top = this.TRACK_2_BASELINE;
    } else if (track === 3) {
      top = this.TRACK_3_BASELINE;
    }

    return top;
  },

  turnLeft: function() {
    if (!this.runner.jumping) {
      this.runner.runAnimationRate = this.RUN_ANIMATION_RATE
      this.bgVelocity = -this.BACKGROUND_VELOCITY;
      this.runnerPageflipInterval = this.RUNNER_PAGE_FLIP_INTERVAL;
      this.runnerArtist.cells = this.runnerCellsLeft;
      this.runner.direction = magicNumbers.direction.LEFT;
    }
  },

  turnRight: function() {
    if (!this.runner.jumping) {
      this.runner.runAnimationRate = this.RUN_ANIMATION_RATE
      this.bgVelocity = this.BACKGROUND_VELOCITY;
      this.runnerPageflipInterval = this.RUNNER_PAGE_FLIP_INTERVAL;
      this.runnerArtist.cells = this.runnerCellsRight;
      this.runner.direction = magicNumbers.direction.RIGHT;
    }
  },

  stopRun: function() {
    if (!this.runner.jumping && this.keyPress == magicNumbers.now_going.NOWHERE) {
      this.bgVelocity = this.STARTING_BACKGROUND_VELOCITY;
      this.runner.runAnimationRate = 0;
    }
  },

  fire: function() {
    this.runner.shoot = true;
  },
  // Sprites..............................................................

  explode: function(sprite, silent) {
    sprite.exploding = true;
    this.explosionAnimator.start(sprite, true); // true means sprite reappears
  },

  equipRunnerForJumping: function() {
    this.runner.JUMP_DURATION = this.RUNNER_JUMP_DURATION; // milliseconds
    this.runner.JUMP_HEIGHT = this.RUNNER_JUMP_HEIGHT;

    this.runner.jumping = false;

    this.runner.ascendAnimationTimer =
      new AnimationTimer(this.runner.JUMP_DURATION / 2,
        AnimationTimer.makeEaseOutTransducer(2.0));

    this.runner.descendAnimationTimer =
      new AnimationTimer(this.runner.JUMP_DURATION / 2,
        AnimationTimer.makeEaseInTransducer(1.1));

    this.runner.jump = function() {
      if (this.jumping) // 'this' is the runner
        return;

      this.runAnimationRate = 0;
      this.jumping = true;
      this.verticalLaunchPosition = this.top;
      this.ascendAnimationTimer.start();
    };
    this.runner.stopJumping = function() {
      this.jumping = false;
      this.ascendAnimationTimer.stop();
      this.descendAnimationTimer.stop();
      this.runAnimationRate = constellationGame.RUN_ANIMATION_RATE;
      constellationGame.stopRun();
      if (constellationGame.keyPress == magicNumbers.now_going.LEFT) {
        constellationGame.turnLeft();
      } else if (constellationGame.keyPress == magicNumbers.now_going.RIGHT) {
        constellationGame.turnRight();
      }

    };
  },

  equipRunner: function() {
    this.runner.track = this.STARTING_RUNNER_TRACK;
    this.runner.direction = this.LEFT;
    this.runner.velocityX = this.STARTING_RUNNER_VELOCITY;

    this.runner.left = this.STARTING_RUNNER_LEFT;
    this.runner.top = this.calculatePlatformTop(this.runner.track) - this.RUNNER_CELLS_HEIGHT;

    this.runner.artist.cells = this.runnerCellsRight;
    this.runner.width = _.max(this.runnerManCellsRight, function(cell) {
      return cell.width
    }).width;
    this.runner.offset = 0;
    this.runner.direction = magicNumbers.direction.RIGHT;
    this.equipRunnerForJumping();
    this.armRunner();
  },

  createPlatformSprites: function() {
    var sprite, pd; // Sprite, Platform data

    for (var i = 0; i < this.platformData.length; ++i) {
      pd = this.platformData[i];
      sprite = new Sprite('platform-' + i, this.platformArtist);
      sprite.common_type = "platform"
      sprite.left = pd.left;
      sprite.width = pd.width;
      sprite.height = pd.height;
      sprite.fillStyle = pd.fillStyle;
      sprite.opacity = pd.opacity;
      sprite.track = pd.track;
      sprite.button = pd.button;
      sprite.pulsate = pd.pulsate;

      sprite.top = this.calculatePlatformTop(pd.track);

      this.platforms.push(sprite);
    }
  },

  // Animation............................................................

  animate: function(now) {
    if (constellationGame.paused) {
      setTimeout(function() {
        requestNextAnimationFrame(constellationGame.animate);
      }, constellationGame.PAUSED_CHECK_INTERVAL);
    } else {
      constellationGame.fps = constellationGame.calculateFps(now);
      constellationGame.draw(now);
      requestNextAnimationFrame(constellationGame.animate);
    }
  },
  togglePausedStateOfAllBehaviors: function() {
    var behavior;

    for (var i = 0; i < this.sprites.length; ++i) {
      sprite = this.sprites[i];

      for (var j = 0; j < sprite.behaviors.length; ++j) {
        behavior = sprite.behaviors[j];

        if (this.paused) {
          if (behavior.pause) {
            behavior.pause(sprite);
          }
        } else {
          if (behavior.unpause) {
            behavior.unpause(sprite);
          }
        }
      }
    }
  },

  togglePaused: function() {
    var now = +new Date();
    this.paused = !this.paused;
    this.togglePausedStateOfAllBehaviors();
    if (this.paused) {
      this.pauseStartTime = now;
    } else {
      this.lastAnimationFrameTime += (now - this.pauseStartTime);
    }
  },

  // ------------------------- INITIALIZATION ----------------------------

  start: function(is_man) {
    this.createSprites(is_man);
    this.initializeImages();
    this.equipRunner();
    this.splashToast('Good Luck!');
  },

  initializeImages: function() {
    var self = this;

    this.background.src = 'images/mb.jpg';
    this.spritesheet.src = 'images/sprites.png';

    this.background.onload = function(e) {
      self.startGame();
    };
  },

  startGame: function() {
    requestNextAnimationFrame(this.animate);
  },

  positionSprites: function(sprites, spriteData) {
    var sprite;
    // debugger;
    for (var i = 0; i < sprites.length; ++i) {
      sprite = sprites[i];

      if (spriteData[i].platformIndex) { // no platform
        this.putSpriteOnPlatform(sprite, this.platforms[spriteData[i].platformIndex]);
      } else {
        sprite.top = spriteData[i].top;
        sprite.left = spriteData[i].left;
      }
    }
  },

  armRunner: function() {
    this.runner.suricane = new Sprite('suricane',
      new SpriteSheetArtist(this.spritesheet, this.orangeSuricaneCells), [
        new suricaneMove(), new Cycle(10), new SuricaneCollide(),
      ]
    );
    this.runner.suricane.common_type = "suricane"
    this.runner.suricane.width = _.max(this.orangeSuricaneCells, function(cell) {
      return cell.width
    }).width;
    this.runner.suricane.height = _.max(this.orangeSuricaneCells, function(cell) {
      return cell.height
    }).height;
    console.log(this.runner.suricane.top)
    this.runner.suricane.top = this.runner.top //+ this.runner.suricane.height / 2;
    this.runner.suricane.left = this.runner.left + this.runner.suricane.width / 2;
    this.runner.suricane.visible = false;

    this.runner.suricane.runner = this.runner;

    this.sprites.push(this.runner.suricane);

  },
  armOldMans: function() {
    var oldMan;

    for (var i = 0; i < this.oldMans.length; ++i) {
      oldMan = this.oldMans[i];
      oldMan.sand = new Sprite('oldMan_sand',
        new SpriteSheetArtist(this.spritesheet, this.sandCells), [new sandMove(), new Cycle(300)]);
      oldMan.sand.vertical = magicNumbers.pegging.BUTTON;
      oldMan.sand.common_type = "bad"
      oldMan.sand.width = _.max(this.sandCells, function(cell) {
        return cell.width
      }).width;
      oldMan.sand.height = _.max(this.sandCells, function(cell) {
        return cell.height
      }).height;

      oldMan.sand.top = oldMan.top + oldMan.height - oldMan.sand.height; //+ oldMan.sand.height / 2;
      oldMan.sand.left = oldMan.left + oldMan.sand.width / 2;
      oldMan.sand.visible = false;

      oldMan.sand.oldMan = oldMan; // Snail sands maintain a reference to their oldMan

      this.sprites.push(oldMan.sand);
    }
  },

  addSpritesToSpriteArray: function() {
    for (var i = 0; i < this.platforms.length; ++i) {
      this.sprites.push(this.platforms[i]);
    }
    for (var i = 0; i < this.orangeStars.length; ++i) {
      this.sprites.push(this.orangeStars[i]);
    }
    for (var i = 0; i < this.clouds.length; ++i) {
      this.sprites.push(this.clouds[i]);
    }
    for (var i = 0; i < this.redStars.length; ++i) {
      this.sprites.push(this.redStars[i]);
    }

    for (var i = 0; i < this.oldMans.length; ++i) {
      this.sprites.push(this.oldMans[i]);
    }
  },

  createOrangeStarSprites: function() {
    var orangeStar;
    for (var i = 0; i < this.orangeStarData.length; ++i) {
      orangeStar = new Sprite('orangeStar', new SpriteSheetArtist(this.spritesheet, this.orangeStarCells), [new Cycle(100, 100)]);
      orangeStar.width = _.max(this.orangeStarCells, function(cell) {
        return cell.width
      }).width;
      orangeStar.height = magicNumbers.ORANGE_STAR_CELLS_HEIGHT;
      this.orangeStars.push(orangeStar);
    }
  },
  createCloudSprites: function() {
    var cloud;
    for (var i = 0; i < this.cloudData.length; ++i) {
      cloud = new Sprite('cloud', new SpriteSheetArtist(this.spritesheet, this.cloudCells), [new Cycle(700)]);
      cloud.vertical = magicNumbers.pegging.TOP;
      cloud.common_type = "bad";
      cloud.width = _.max(this.cloudCells, function(cell) {
        return cell.width
      }).width;
      cloud.height = _.max(this.cloudCells, function(cell) {
        return cell.height
      }).height;
      this.clouds.push(cloud);
    }
  },
  createRedStarSprites: function() {
    var redStar;
    for (var i = 0; i < this.redStarData.length; ++i) {
      redStar = new Sprite('redStar', new SpriteSheetArtist(this.spritesheet, this.redStarCells), [new Cycle(200),
        new BounceBehavior()
      ]);
      redStar.width = _.max(this.redStarCells, function(cell) {
        return cell.width
      }).width;
      redStar.height = magicNumbers.ORANGE_STAR_CELLS_HEIGHT;
      this.redStars.push(redStar);
    }
  },

  createOldManSprites: function() {
    var oldMan,
      oldManArtist = new SpriteSheetArtist(this.spritesheet,
        this.oldManCellsRight);

    for (var i = 0; i < this.oldManData.length; ++i) {
      oldMan = new Sprite('oldMan', oldManArtist, [
        new Pace(this.oldManCellsLeft, this.oldManCellsRight),
        new oldManShoot(),
        new Cycle(250)
      ]);
      oldMan.common_type = "bad"

      oldMan.width = _.max(this.oldManCellsRight, function(cell) {
        return cell.width
      }).width;
      oldMan.height = magicNumbers.OLD_MAN_CELLS_HEIGHT;
      oldMan.velocityX = this.OLDMAN_PACE_VELOCITY;
      oldMan.direction = this.RIGHT;
      this.oldMans.push(oldMan);
    }
  },

  updateSprites: function(now) {
    var sprite;

    for (var i = 0; i < this.sprites.length; ++i) {
      sprite = this.sprites[i];
      if (sprite.visible && this.spriteInView(sprite)) {
        sprite.update(now, this.fps, this.context);
      }
    }
  },

  drawSprites: function() {
    var sprite;

    for (var i = 0; i < this.sprites.length; ++i) {
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
    sprite.top = platformSprite.top - sprite.height;
    sprite.left = platformSprite.left;
    sprite.platform = platformSprite;
  },

  createSprites: function(is_man) {
    if (is_man) {
      this.runnerCellsRight = this.runnerManCellsRight;
      this.runnerCellsLeft = this.runnerManCellsLeft;
    } else {
      this.runnerCellsRight = this.runnerWomanCellsRight;
      this.runnerCellsLeft = this.runnerWomanCellsLeft;
    }
    this.createPlatformSprites(); // Platforms must be created first

    // this.createBatSprites();
    this.createOrangeStarSprites();
    this.createCloudSprites();
    this.createRedStarSprites();
    // this.createButtonSprites();
    // this.createCoinSprites();
    // this.createRubySprites();
    // this.createSapphireSprites();
    this.createOldManSprites();

    this.initializeSprites();

    this.addSpritesToSpriteArray();
  },

  initializeSprites: function() {
    for (var i = 0; i < constellationGame.sprites.length; ++i) {
      constellationGame.sprites[i].offset = 0;
    }
    // this.positionSprites(this.bats, this.batData);
    this.positionSprites(this.orangeStars, this.orangeStarData);
    this.positionSprites(this.clouds, this.cloudData);
    this.positionSprites(this.redStars, this.redStarData);
    // this.positionSprites(this.buttons, this.buttonData);
    // this.positionSprites(this.coins, this.coinData);
    // this.positionSprites(this.rubies, this.rubyData);
    // this.positionSprites(this.sapphires, this.sapphireData);
    this.positionSprites(this.oldMans, this.oldManData);
    // this.positionSprites(this.snailBombs, this.snailBombData);
    this.armOldMans();

  },

  // Toast................................................................

  splashToast: function(text, howLong) {
    howLong = howLong || this.DEFAULT_TOAST_TIME;

    toast.style.display = 'block';
    toast.innerHTML = text;

    setTimeout(function(e) {
      if (constellationGame.windowHasFocus) {
        toast.style.opacity = 1.0; // After toast is displayed
      }
    }, 50);

    setTimeout(function(e) {
      if (constellationGame.windowHasFocus) {
        toast.style.opacity = 0; // Starts CSS3 transition
      }

      setTimeout(function(e) {
        if (constellationGame.windowHasFocus) {
          toast.style.display = 'none';
        }
      }, 480);
    }, howLong);
  },
};

// Event handlers.......................................................

window.onkeydown = function(e) {
  if (constellationGame == null) {
    return;
  }
  var key = e.keyCode;
  if (key === 80 || (constellationGame.paused && key !== 80)) { // 'p'
    constellationGame.togglePaused();
  }

  if (key === 68 || key === 37) { // 'd' or left arrow
    if (constellationGame.keyPress != magicNumbers.now_going.LEFT) {
      constellationGame.turnLeft();
      constellationGame.keyPress = magicNumbers.now_going.LEFT;
    }
  } else if (key === 75 || key === 39) { // 'k'or right arrow
    if (constellationGame.keyPress != magicNumbers.now_going.RIGHT) {
      constellationGame.turnRight();
      constellationGame.keyPress = magicNumbers.now_going.RIGHT;
    }

  } else if (key === 74 || key === 32) { // 'j' or space
    e.preventDefault();
    constellationGame.runner.jump();
  }

  constellationGame.runner.top = constellationGame.calculatePlatformTop(constellationGame.runner.track) -
    constellationGame.RUNNER_CELLS_HEIGHT;
};

window.onkeyup = function(e) {
  if (constellationGame == null) {
    return;
  }
  var key = e.keyCode;

  if (key === 68 || key === 37) { // 'd' or left arrow
    constellationGame.keyPress = magicNumbers.now_going.NOWHERE;
    constellationGame.stopRun();
  } else if (key === 75 || key === 39) { // 'k'or right arrow
    constellationGame.keyPress = magicNumbers.now_going.NOWHERE;
    constellationGame.stopRun();
  } else if (key === 70) { // 'f'
    constellationGame.fire();
  }
};

window.onblur = function(e) { // pause if unpaused
  if (constellationGame == null) {
    return;
  }
  constellationGame.windowHasFocus = false;

  if (!constellationGame.paused) {
    constellationGame.togglePaused();
  }
};

window.onfocus = function(e) { // unpause if paused
  if (constellationGame == null) {
    return;
  }
  var originalFont = constellationGame.toast.style.fontSize;
  constellationGame.windowHasFocus = true;

  if (constellationGame.paused) {
    constellationGame.toast.style.font = '128px fantasy';

    constellationGame.splashToast('3', 500); // Display 3 for one half second
    setTimeout(function(e) {
      if (!constellationGame.paused) {
        constellationGame.togglePaused();
      }

    }, 250)
    setTimeout(function(e) {
      constellationGame.splashToast('2', 500); // Display 2 for one half second
      if (!constellationGame.paused) {
        constellationGame.togglePaused();
      }
      setTimeout(function(e) {
        if (!constellationGame.paused) {
          constellationGame.togglePaused();
        }

        constellationGame.splashToast('1', 500); // Display 1 for one half second

        setTimeout(function(e) {
          if (constellationGame.windowHasFocus && constellationGame.paused) {
            constellationGame.togglePaused();
          }

          setTimeout(function(e) { // Wait for '1' to disappear
            constellationGame.toast.style.fontSize = originalFont;
          }, 2000);
        }, 1000);
      }, 1000);
    }, 1000);
  }
};

// Launch game.........................................................
var manOrWomanElement = document.getElementById('man-or-woman');
var constellationGame = null;
manOrWomanElement.style.display = 'block';

setTimeout(function() {
  manOrWomanElement.style.opacity = 1.0;
}, 10);

document.getElementById('man-play').onclick = function(e) {
  e.preventDefault();
  constellationGame = new ConstellationGame();
  constellationGame.start(true);
  var CREDITS_REVEAL_DELAY = 2000;

  manOrWomanElement.style.opacity = 0;

  setTimeout(function(e) {
    manOrWomanElement.style.display = 'none';
  }, CREDITS_REVEAL_DELAY);

};

document.getElementById('woman-play').onclick = function(e) {
  e.preventDefault();
  constellationGame = new ConstellationGame();
  constellationGame.start(false);
  var CREDITS_REVEAL_DELAY = 2000;

  manOrWomanElement.style.opacity = 0;

  setTimeout(function(e) {
    manOrWomanElement.style.display = 'none';
  }, CREDITS_REVEAL_DELAY);

};

// чтобы располагать спрайты
document.getElementById('game-canvas').onclick = function(e) {
  var w = e.offsetX + constellationGame.spriteOffset;
  var h = e.offsetY;
  console.log(w, h)
}
