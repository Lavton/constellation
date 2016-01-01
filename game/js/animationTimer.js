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

// AnimationTimer..................................................................
//
// An animation runs for a duration, in milliseconds. It's up to you,
// however, to start and stop the animation -- animation timers do not stop
// automatically. You can check to see if an animation timer is over with the
// isOver() method, and you can see if an animation is running with
// isRunning(). Note that animations can be over, but still running.

AnimationTimer = function (duration)  {
   if (duration !== undefined) this.duration = duration;
   else                        this.duration = 1000;

   this.stopwatch = new Stopwatch();
};

AnimationTimer.prototype = {
   start: function () {
      this.stopwatch.start();
   },

   stop: function () {
      this.stopwatch.stop();
   },

   pause: function () {
      this.stopwatch.pause();
   },

   unpause: function () {
      this.stopwatch.unpause();
   },

   isPaused: function () {
      return this.stopwatch.isPaused();
   },

   getRealElapsedTime: function () {
      return this.stopwatch.getElapsedTime();
   },
   
   getElapsedTime: function () {
      return this.stopwatch.getElapsedTime();
   },

   isRunning: function() {
      return this.stopwatch.running;
   },
   
   isOver: function () {
      return this.stopwatch.getElapsedTime() > this.duration;
   },

   reset: function() {
      this.stopwatch.reset();
   }
};
