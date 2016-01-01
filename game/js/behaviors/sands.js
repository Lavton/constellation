oldManShoot = function() {
  this.lastAdvanceTime = 0; //  milliseconds
};

oldManShoot.prototype = {
  execute: function(sprite, time, fps) {
    var sand = sprite.sand;

    if (!sand.visible && sprite.artist.cellIndex === 2 && sprite.direction == magicNumbers.direction.LEFT) {
      sand.left = sprite.left;
      sand.visible = true;
    }
  }
};

suricaneMove = function() {
  this.lastAdvanceTime = 0; //  milliseconds
  this.spriteVelocity
};

suricaneMove.prototype = {
  execute: function(sprite, time, fps) {
    if (sprite.visible && constellationGame.spriteInView(sprite)) {
      sprite.left -= -magicNumbers.SAND_VALOCITY / fps;
    }

    if (!constellationGame.spriteInView(sprite)) {
      sprite.visible = false;
    }
  }
};
