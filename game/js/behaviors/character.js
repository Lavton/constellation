/*манипуляторы главного героя*/
// бег
Run = function() {
  this.lastAdvanceTime = 0; //  milliseconds
};

Run.prototype = {
  execute: function(sprite, time, fps) {
    // Realize that this is a method in an object (runBehavior), that resides
    // in another object (magicNumbers), so the 'this' reference in this method
    // refers to runBehavior, not magicNumbers.
    if (sprite.runAnimationRate === 0) {
      return;
    }
    if (this.lastAdvanceTime === 0) { // skip first time
      this.lastAdvanceTime = time;
    } else if (time - this.lastAdvanceTime > 1000 / sprite.runAnimationRate) {
      sprite.artist.advance();
      this.lastAdvanceTime = time;
    }
  }
};

// стрельба
runnerShoot = function() {};

runnerShoot.prototype = {
  execute: function(sprite, time, fps) {
    var suricane = sprite.suricane;
    if (!suricane.visible && sprite.shoot && sprite.direction == magicNumbers.direction.RIGHT) {
      suricane.left = constellationGame.spriteOffset + constellationGame.STARTING_RUNNER_LEFT + sprite.width;
      suricane.top = constellationGame.runner.top //+ this.runner.suricane.height / 2;
      suricane.visible = true;
      constellationGame.playSound(constellationGame.suricaneSound)
    } else if (sprite.shoot) {
    }
    sprite.shoot = false;
  }
};

sandMove = function() {
  this.lastAdvanceTime = 0; //  milliseconds
  this.spriteVelocity
};

sandMove.prototype = {
  execute: function(sprite, time, fps) {
    if (sprite.visible && constellationGame.spriteInView(sprite)) {
      sprite.left -= magicNumbers.SAND_VALOCITY / fps;
    }

    if (!constellationGame.spriteInView(sprite)) {
      sprite.visible = false;
    }
  }
};


Jump = function() {};

Jump.prototype = {
  pause: function(sprite) {
    if (sprite.ascendAnimationTimer.isRunning()) {
      sprite.ascendAnimationTimer.pause();
    } else if (!sprite.descendAnimationTimer.isRunning()) {
      sprite.descendAnimationTimer.pause();
    }
  },

  unpause: function(sprite) {
    if (sprite.ascendAnimationTimer.isRunning()) {
      sprite.ascendAnimationTimer.unpause();
    } else if (sprite.descendAnimationTimer.isRunning()) {
      sprite.descendAnimationTimer.unpause();
    }
  },

  isJumpOver: function(sprite) {
    return !sprite.ascendAnimationTimer.isRunning() &&
      !sprite.descendAnimationTimer.isRunning();
  },

  // Ascent...............................................................

  isAscending: function(sprite) {
    return sprite.ascendAnimationTimer.isRunning();
  },

  ascend: function(sprite) {
    var elapsed = sprite.ascendAnimationTimer.getElapsedTime(),
      deltaH = elapsed / (sprite.JUMP_DURATION / 2) * sprite.JUMP_HEIGHT;

    sprite.top = sprite.verticalLaunchPosition - deltaH;
  },

  isDoneAscending: function(sprite) {
    return sprite.ascendAnimationTimer.getElapsedTime() > sprite.JUMP_DURATION / 2;
  },

  finishAscent: function(sprite) {
    sprite.jumpApex = sprite.top;
    sprite.ascendAnimationTimer.stop();
    sprite.descendAnimationTimer.start();
  },

  // Descents.............................................................

  isDescending: function(sprite) {
    return sprite.descendAnimationTimer.isRunning();
  },

  descend: function(sprite, verticalVelocity, fps) {
    var elapsed = sprite.descendAnimationTimer.getElapsedTime(),
      deltaH = elapsed / (sprite.JUMP_DURATION / 2) * sprite.JUMP_HEIGHT;

    sprite.top = sprite.jumpApex + deltaH;
  },

  isDoneDescending: function(sprite) {
    return sprite.descendAnimationTimer.getElapsedTime() > sprite.JUMP_DURATION / 2;
  },

  finishDescent: function(sprite) {
    sprite.stopJumping();

    if (constellationGame.isOverPlatform(sprite) !== -1) {
      sprite.top = sprite.verticalLaunchPosition;
    } else {
      sprite.fall(magicNumbers.GRAVITY_FORCE *
        (sprite.descendAnimationTimer.getElapsedTime() / 1000) *
        magicNumbers.PIXELS_PER_METER);
    }

  },

  // Execute..............................................................

  execute: function(sprite, time, fps) {
    if (!sprite.jumping || sprite.exploding) {
      return;
    }

    if (this.isJumpOver(sprite)) {
      sprite.jumping = false;
      return;
    }


    if (this.isAscending(sprite)) {
      if (!this.isDoneAscending(sprite)) {
        this.ascend(sprite);
      } else {
        this.finishAscent(sprite);
      }
    } else if (this.isDescending(sprite)) {
      if (!this.isDoneDescending(sprite)) {
        this.descend(sprite);
      } else {
        this.finishDescent(sprite);
      }
    }
  }
};


Collide = function() {};

Collide.prototype = {
  execute: function(sprite, time, fps, context) {
    var otherSprite;
    for (var i = 0; i < constellationGame.sprites.length; ++i) {
      otherSprite = constellationGame.sprites[i];

      if (this.isCandidateForCollision(sprite, otherSprite)) {
        if (this.didCollide(sprite, otherSprite, context)) {
          this.processCollision(sprite, otherSprite);
        }
      }
    }
  },

  isCandidateForCollision: function(sprite, otherSprite) {
    return sprite !== otherSprite &&
      sprite.visible && otherSprite.visible &&
      !sprite.exploding && !otherSprite.exploding &&
      otherSprite.left - otherSprite.offset <
      sprite.left - sprite.offset + sprite.width * 2 &&
      otherSprite.type != "suricane" &&
      !otherSprite.exploding;
  },

  didSandCollideWithRunner: function(left, top, right, bottom,
    sand, context) {
    // Determine if the center of the snail bomb lies within
    // the runner's bounding box  
    if (!context) {
      return false;
    }
    context.beginPath();
    context.rect(left, top, right - left, bottom - top);

    return context.isPointInPath(
      sand.left - sand.offset + sand.width / 2,
      sand.top + sand.height / 2);
  },

  didRunnerCollideWithOtherSprite: function(left, top, right, bottom,
    centerX, centerY,
    otherSprite, context) {
    // Determine if either of the runner's four corners or its
    // center lie within the other sprite's bounding box. 
    if (!context) {
      return false;
    }
    context.beginPath();
    context.rect(otherSprite.left - otherSprite.offset, otherSprite.top,
      otherSprite.width, otherSprite.height);

    return context.isPointInPath(left, top) ||
      context.isPointInPath(right, top) ||

      context.isPointInPath(centerX, centerY) ||

      context.isPointInPath(left, bottom) ||
      context.isPointInPath(right, bottom);
  },

  didCollide: function(sprite, otherSprite, context) {
    var MARGIN_TOP = 10,
      MARGIN_LEFT = 10,
      MARGIN_RIGHT = 10,
      MARGIN_BOTTOM = 0,
      left = sprite.left + sprite.offset + MARGIN_LEFT,
      right = sprite.left + sprite.offset + sprite.width - MARGIN_RIGHT,
      top = sprite.top + MARGIN_TOP,
      bottom = sprite.top + sprite.height - MARGIN_BOTTOM,
      centerX = left + sprite.width / 2,
      centerY = sprite.top + sprite.height / 2;

    if (otherSprite.type === 'oldMan_sand') {
      return this.didSandCollideWithRunner(left, top, right, bottom,
        otherSprite, context);
    } else {
      return this.didRunnerCollideWithOtherSprite(left, top, right, bottom,
        centerX, centerY,
        otherSprite, context);
    }
  },

  processCollision: function(sprite, otherSprite) {
    if (otherSprite.value) { // Modify Snail Bait sprites so they have values
      // Keep score...
    }

    if ("good" === otherSprite.common_type) {
      otherSprite.visible = false;
      constellationGame.playSound(constellationGame.chimesSound);
    }

    if ("bad" === otherSprite.common_type) {
      if ("cloud" === otherSprite.type && otherSprite.artist.cellIndex != 0) {
        return
      }
      constellationGame.explode(sprite);
    }
    if (sprite.jumping && 'platform' === otherSprite.common_type) {
      this.processPlatformCollisionDuringJump(sprite, otherSprite);
    }
  },

  processPlatformCollisionDuringJump: function(sprite, platform) {
    var isDescending = sprite.descendAnimationTimer.isRunning();
    sprite.stopJumping();

    if (isDescending) { // Collided with platform while descending
      // land on platform
      sprite.track = platform.track;
      sprite.top = constellationGame.calculatePlatformTop(sprite.track) - sprite.height;
    } else { // Collided with platform while ascending
      constellationGame.playSound(constellationGame.plopSound);
      sprite.fall();
    }
  }
};


Fall = function() {};

Fall.prototype = {
  isOutOfPlay: function(sprite) {
    return sprite.top > constellationGame.TRACK_1_BASELINE;
  },

  willFallBelowCurrentTrack: function(sprite, deltaY) {
    return sprite.top + sprite.height + deltaY >
      constellationGame.calculatePlatformTop(sprite.track);
  },

  fallOnPlatform: function(sprite) {
    sprite.top = constellationGame.calculatePlatformTop(sprite.track) - sprite.height;
    sprite.stopFalling();
    constellationGame.playSound(constellationGame.thudSound);
  },

  setSpriteVelocity: function(sprite) {
    var fallingElapsedTime;

    sprite.velocityY = sprite.initialVelocityY + magicNumbers.GRAVITY_FORCE *
      (sprite.fallAnimationTimer.getElapsedTime() / 1000) *
      magicNumbers.PIXELS_PER_METER;
    // debugger;
  },

  calculateVerticalDrop: function(sprite, fps) {
    return sprite.velocityY / fps;
  },

  isPlatformUnderneath: function(sprite) {
    return constellationGame.isOverPlatform(sprite) !== -1;
  },

  execute: function(sprite, time, fps) {
    var deltaY;

    if (sprite.jumping) {
      return;
    }

    if (this.isOutOfPlay(sprite) || sprite.exploding) {
      if (sprite.falling) {
        sprite.stopFalling();
      }
      return;
    }

    if (!sprite.falling) {
      if (!sprite.exploding && !this.isPlatformUnderneath(sprite)) {
        sprite.fall();
      }
      return;
    }
    this.setSpriteVelocity(sprite);
    deltaY = this.calculateVerticalDrop(sprite, fps);
    if (!this.willFallBelowCurrentTrack(sprite, deltaY)) {
      sprite.top += deltaY;
    } else { // will fall below current track
      if (this.isPlatformUnderneath(sprite)) {
        this.fallOnPlatform(sprite);
        sprite.stopFalling();
      } else {
        sprite.track--;

        sprite.top += deltaY;

        if (sprite.track === 0) {
          constellationGame.playSound(constellationGame.fallingWhistleSound);
        }
      }
    }
  }
};
