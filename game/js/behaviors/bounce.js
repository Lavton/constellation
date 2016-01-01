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

// BOUNCE: Bounce a sprite up and down, easing out on the way up
//         and easing in on the way down.

Bounce = function (riseTime, fallTime, distance) {
   this.riseTime = riseTime || 800;
   this.fallTime = fallTime || 800;
   this.distance = distance || 30;

   this.riseTimer = new AnimationTimer(this.riseTime,
                                       AnimationTimer.makeEaseOut(1));

   this.fallTimer = new AnimationTimer(this.fallTime,
                                       AnimationTimer.makeEaseIn(1));
   this.paused = false;
}

Bounce.prototype = {
   pause: function() {
      if (!this.riseTimer.isPaused()) {
         this.riseTimer.pause();
      }

      if (!this.fallTimer.isPaused()) {
         this.fallTimer.pause();
      }

      this.paused = true;
   },

   unpause: function() {
      if (this.riseTimer.isPaused()) {
         this.riseTimer.unpause();
      }

      if (this.fallTimer.isPaused()) {
         this.fallTimer.unpause();
      }

      this.paused = false;
   },
   
   startRising: function (sprite) {
      this.baseline = sprite.top;
      this.bounceStart = sprite.top;

      this.riseTimer.start();
   },
      
   rise: function (sprite) {
      var elapsedTime = this.riseTimer.getElapsedTime();
      sprite.top = this.baseline - parseFloat(
                      (elapsedTime / this.riseTime) * this.distance);
   },

   finishRising: function (sprite) {
      this.riseTimer.stop();
      this.baseline = sprite.top;
      this.fallTimer.start();
   },
      
   isFalling: function () {
      return this.fallTimer.isRunning();
   },
      
   isRising: function () {
      return this.riseTimer.isRunning();
   },

   fall: function (sprite) {
      var elapsedTime = this.fallTimer.getElapsedTime();  
      sprite.top = this.baseline +
      parseFloat((elapsedTime / this.fallTime) * this.distance);
   },

   finishFalling: function (sprite) {
      this.fallTimer.stop();
      sprite.top = this.bounceStart;
      this.startRising(sprite);
   },
      
   execute: function(sprite, time, fps) {
      // If nothing's happening, start rising and return

      if (this.paused || !this.isRising() && ! this.isFalling()) {
         this.startRising(sprite);
         return;
      }

      if(this.isRising()) {   // Rising
         if(!this.riseTimer.isOver()) {  // Not done rising
            this.rise(sprite);
         }
         else {  // Done rising
            this.finishRising(sprite);
         }
      }
      else if(this.isFalling()) { // Falling
         if(!this.fallTimer.isOver()) {     // Not done falling
            this.fall(sprite);
         }
         else { // Done falling
            this.finishFalling(sprite);
         }
      }
   }
};
