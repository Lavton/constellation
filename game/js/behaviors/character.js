/*манипуляторы главного героя*/
// бег
Run = function() {
  this.lastAdvanceTime = 0; //  milliseconds
};

Run.prototype = {
  execute: function(sprite, time, fps) {
    // Realize that this is a method in an object (runBehavior), that resides
    // in another object (snailBait), so the 'this' reference in this method
    // refers to runBehavior, not snailBait.
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
runnerShoot = function() {
};

runnerShoot.prototype = {
  execute: function(sprite, time, fps) {
    var suricane = sprite.suricane;
    if (!suricane.visible && sprite.shoot && sprite.direction == magicNumbers.direction.RIGHT ) {
      suricane.left = constellationGame.spriteOffset + constellationGame.STARTING_RUNNER_LEFT + sprite.width;
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
