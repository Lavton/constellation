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

sandMove = function() {
  this.lastAdvanceTime = 0; //  milliseconds
  this.spriteVelocity
};

sandMove.prototype = {
  execute: function(sprite, time, fps) {
    if (sprite.visible && constellationGame.spriteInView(sprite)) {
      // if (sprite.oldMan.direction == magicNumbers.direction.LEFT) {
         sprite.left -= magicNumbers.SAND_VALOCITY / fps;
      // } else {
         // sprite.left -= -magicNumbers.SAND_VALOCITY / fps;
      // }
    }

    if (!constellationGame.spriteInView(sprite)) {
      sprite.visible = false;
    }
  }
};
