// sand and suricane behaviors

oldManShoot = function() {
  this.lastAdvanceTime = 0; //  milliseconds
};

oldManShoot.prototype = {
  execute: function(sprite, time, fps) {
    var sand = sprite.sand;
    if (!constellationGame.spriteInView(sprite)) {
      return;
    }
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



SuricaneCollide = function() {};

SuricaneCollide.prototype = {
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
      otherSprite.common_type == "bad" &&
      otherSprite.type != "runner" &&
      otherSprite.type != "oldMan_sand" &&
      otherSprite.left - otherSprite.offset <
      sprite.left - sprite.offset + sprite.width * 10 &&
      otherSprite.left - otherSprite.offset >
      sprite.left - sprite.offset - sprite.width * 10;
  },

  didSuricaneCollideWithOtherSprite: function(left, top, right, bottom,
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
    if (context.isPointInPath(left, top) ||
      context.isPointInPath(right, top) ||

      context.isPointInPath(centerX, centerY) ||

      context.isPointInPath(left, bottom) ||
      context.isPointInPath(right, bottom)) {}
    return context.isPointInPath(left, top) ||
      context.isPointInPath(right, top) ||

      context.isPointInPath(centerX, centerY) ||

      context.isPointInPath(left, bottom) ||
      context.isPointInPath(right, bottom);
  },

  didCollide: function(sprite, otherSprite, context) {
    var left = sprite.left - sprite.offset,
      right = sprite.left - sprite.offset + sprite.width,
      top = sprite.top,
      bottom = sprite.top + sprite.height,
      centerX = left + sprite.width / 2,
      centerY = sprite.top + sprite.height / 2;

    return this.didSuricaneCollideWithOtherSprite(left, top, right, bottom,
      centerX, centerY,
      otherSprite, context);
  },

  processCollision: function(sprite, otherSprite) {
    if (otherSprite.value) { // Modify Snail Bait sprites so they have values
      // Keep score...
    }

    otherSprite.visible = false;
    sprite.visible = false;

  },
};
