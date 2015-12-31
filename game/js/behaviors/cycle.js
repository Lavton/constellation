/*
 * Copyright (C) 2012 David Geary.
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

// CYCLE: For sprites that have a spritesheet artist, this behavior
//        advances the sprite artist through the sprite's images.

Cycle = function (duration, interval) {
   this.duration = duration || 0;  //  milliseconds
   this.interval = interval || 0;
   this.lastAdvance = 0;
};

Cycle.prototype = { 
   execute: function(sprite, time, fps) {
      if (this.lastAdvance === 0) {
         this.lastAdvance = time;
      }

      if (this.interval && sprite.artist.cellIndex === 0) {
         if (time - this.lastAdvance > this.interval) {
            sprite.artist.advance();
            this.lastAdvance = time;
         }
      }
      else  if (time - this.lastAdvance > this.duration) {
         sprite.artist.advance();
         this.lastAdvance = time;
      }
   }
};
