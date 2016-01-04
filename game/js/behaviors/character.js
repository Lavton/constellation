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
    } else if (sprite.shoot) {
      console.log(suricane.visible, sprite.direction)
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
    sprite.top = sprite.verticalLaunchPosition;
    sprite.jumping = false;
    sprite.runAnimationRate = magicNumbers.RUN_ANIMATION_RATE;
    sprite.ascendAnimationTimer.stop();
    sprite.descendAnimationTimer.stop();
    constellationGame.stopRun();
    if (constellationGame.keyPress == magicNumbers.now_going.LEFT) {
      constellationGame.turnLeft();
    } else if (constellationGame.keyPress == magicNumbers.now_going.RIGHT) {
      constellationGame.turnRight();
    }
  },

  // Execute..............................................................

  execute: function(sprite, time, fps) {
    if (!sprite.jumping) {
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
