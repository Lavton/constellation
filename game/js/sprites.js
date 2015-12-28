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
// Sprite Artists............................................................

// Artists draw sprites with a draw(sprite, context) method. ImageArtists
// draw an image for their sprite.

var ImageArtist = function (imageUrl) {
   this.image = new Image;
   this.image.src = imageUrl;
};

ImageArtist.prototype = {
   image: undefined,

   drawSpriteImage: function (sprite, context) {
      context.drawImage(this.image, sprite.left, sprite.top);
   },
   
   draw: function (sprite, context) {
      this.drawSpriteImage(sprite, context);   
   }
};

SpriteSheetArtist = function (spritesheet, cells) {
   this.cells = cells;
   this.spritesheet = spritesheet;
   this.cellIndex = 0;
};

SpriteSheetArtist.prototype = {
   advance: function () {
      if (this.cellIndex == this.cells.length-1) {
         this.cellIndex = 0;
      }
      else {
         this.cellIndex++;
      }
   },
   
   draw: function (sprite, context) {
      var cell = this.cells[this.cellIndex];

      context.drawImage(this.spritesheet, cell.left, cell.top,
                                          cell.width, cell.height,
                                          sprite.left, sprite.top,
                                          cell.width, cell.height);
   }
};

// Sprite Animators...........................................................

var SpriteAnimator = function (cells, duration, callback) {
   this.cells = cells;
   this.duration = duration || 1000;
   this.callback = callback;
};

SpriteAnimator.prototype = {
   start: function (sprite, reappear) {
      var originalCells = sprite.artist.cells,
          originalIndex = sprite.artist.cellIndex,
          self = this;

      sprite.artist.cells = this.cells;
      sprite.artist.cellIndex = 0;
      
      setTimeout(function() {
         sprite.artist.cells = originalCells;
         sprite.artist.cellIndex = originalIndex;

         sprite.visible = reappear;

         if (self.callback) {
            self.callback(sprite, self);
         }
      }, self.duration); 
   },
};

// Sprites....................................................................

// Sprites have a type, an artist, and an array of behaviors. Sprites can
// be updated and drawn.
//
// A sprite's artist draws the sprite: draw(sprite, context)
// A sprite's behavior executes: execute(sprite, context, time, fps)

var Sprite = function (type, artist, behaviors) {
   this.type = type || '';
   this.artist = artist || undefined;
   this.behaviors = behaviors || [];

   this.left = 0;
   this.top = 0;
   this.width = 10;
   this.height = 10;
	this.velocityX = 0;
	this.velocityY = 0;
	this.opacity = 1.0;
   this.visible = true;

   return this;
};

Sprite.prototype = {
	draw: function (context) {
     context.save();
     context.globalAlpha = this.opacity;
      
     if (this.artist && this.visible) {
        this.artist.draw(this, context);
     }
     context.restore();
	},

   update: function (context, time, fps) {
      for (var i = 0; i < this.behaviors.length; ++i) {
         if (this.behaviors[i] === undefined) { // Modified while looping?
            return;
         }

         this.behaviors[i].execute(this, context, time, fps);
      }
   }
};
