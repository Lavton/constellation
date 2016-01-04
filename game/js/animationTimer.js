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

// snailBait constructor --------------------------------------------
// AnimationTimer..................................................................
//
// An animation runs for a duration, in milliseconds. 
//
// You can supply an optional transducer function that modifies the percent
// completed for the animation. That modification lets you incorporate
// non-linear motion, such as ease-in, ease-out, elastic, etc.

AnimationTimer = function (duration, transducer)  {
   this.transducer = transducer;

   if (duration !== undefined) this.duration = duration;
   else                        this.duration = 1000;

   this.stopwatch = new Stopwatch();
};

AnimationTimer.prototype = {
       start: function () { this.stopwatch.start();           },
        stop: function () { this.stopwatch.stop();            },
       pause: function () { this.stopwatch.pause();           },
     unpause: function () { this.stopwatch.unpause();         },
    isPaused: function () { return this.stopwatch.isPaused(); },
   isRunning: function () { return this.stopwatch.running;    },
       reset: function () { this.stopwatch.reset();           },

   isExpired: function () {
      return this.stopwatch.getElapsedTime() > this.duration;
   },

   getRealElapsedTime: function () {
      return this.stopwatch.getElapsedTime();
   },
   
   getElapsedTime: function () {
      var elapsedTime = this.stopwatch.getElapsedTime(),
          percentComplete = elapsedTime / this.duration;

      if (percentComplete >= 1) {
         percentComplete = 1.0;
      }

      if (this.transducer !== undefined && percentComplete > 0) {
         elapsedTime =  elapsedTime *
                        (this.transducer(percentComplete) / percentComplete);
      }

      return elapsedTime;
   },

};

AnimationTimer.makeEaseOutTransducer = function (strength) {
   return function (percentComplete) {
      strength = strength ? strength : 1.0;

      return 1 - Math.pow(1 - percentComplete, strength*2);
   };
};

AnimationTimer.makeEaseInTransducer = function (strength) {
   strength = strength ? strength : 1.0;

   return function (percentComplete) {
      return Math.pow(percentComplete, strength*2);
   };
};

AnimationTimer.makeEaseInOutTransducer = function () {
   return function (percentComplete) {
      return percentComplete - Math.sin(percentComplete*2*Math.PI) / (2*Math.PI);
   };
};

AnimationTimer.makeElasticTransducer = function (passes) {
   passes = passes || 3;

   return function (percentComplete) {
       return ((1-Math.cos(percentComplete * Math.PI * passes)) *
               (1 - percentComplete)) + percentComplete;
   };
};

AnimationTimer.makeBounceTransducer = function (bounces) {
   var fn = AnimationTimer.makeElasticTransducer(bounces);

   bounces = bounces || 2;

   return function (percentComplete) {
      percentComplete = fn(percentComplete);
      return percentComplete <= 1 ? percentComplete : 2-percentComplete;
   }; 
};

AnimationTimer.makeLinearTransducer = function () {
   return function (percentComplete) {
      return percentComplete;
   };
};
