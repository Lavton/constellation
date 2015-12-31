/*перемещение вдоль платформы*/

Pace = function(leftCells,  rightCells) {
  this.lastAdvanceTime = 0; //  milliseconds
  this.wasTurned = false;
  this.leftCells = leftCells;
  this.rightCells = rightCells
};

Pace.prototype = {
  checkDirection: function(sprite) {
    this.wasTurned = false;
    var sRight = sprite.left + sprite.width,
      pRight = sprite.platform.left + sprite.platform.width;

    if (sRight > pRight && sprite.direction === constellationGame.RIGHT) {
      sprite.direction = constellationGame.LEFT;
      sprite.artist.cells=this.leftCells;
      this.wasTurned = true;
    } else if (sprite.left < sprite.platform.left &&
      sprite.direction === constellationGame.LEFT) {
      sprite.direction = constellationGame.RIGHT;
      sprite.artist.cells=this.rightCells;
      this.wasTurned = true;
    }
  },

  moveSprite: function(sprite, fps) {
    var pixelsToMove = sprite.velocityX / fps;
    if (sprite.direction === constellationGame.RIGHT) {
      sprite.left += pixelsToMove;
    } else {
      sprite.left -= pixelsToMove;
    }

  },

  execute: function(sprite, time, fps) {
    this.checkDirection(sprite);
    this.moveSprite(sprite, fps);
  }
};
