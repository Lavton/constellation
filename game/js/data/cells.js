var cellsFactory = function() {

  // Бегущая девочка.........................................................

  this.runnerWomanCellsRight = [{
      left: 58,
      top: 13,
      width: 46,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, {
      left: 102,
      top: 13,
      width: 45,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, {
      left: 148,
      top: 12,
      width: 43,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, {
      left: 194,
      top: 10,
      width: 44,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, {
      left: 238,
      top: 13,
      width: 48,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, {
      left: 289,
      top: 14,
      width: 40,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, {
      left: 330,
      top: 12,
      width: 44,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, {
      left: 11,
      top: 9,
      width: 41,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, ],

    this.runnerWomanCellsLeft = [{
      left: 281,
      top: 87,
      width: 47,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, {
      left: 328,
      top: 87,
      width: 41,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, {
      left: 7,
      top: 86,
      width: 44,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, {
      left: 52,
      top: 87,
      width: 40,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, {
      left: 96,
      top: 86,
      width: 48,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, {
      left: 141,
      top: 87,
      width: 44,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, {
      left: 192,
      top: 87,
      width: 43,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, {
      left: 236,
      top: 87,
      width: 45,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, ];


  // Бегущий парень.........................................................

  this.runnerManCellsRight = [
  {
      left: 128,
      top: 165,
      width: 62,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    },
    {
      left: 194,
      top: 165,
      width: 57,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 
    {
      left: 262,
      top: 165,
      width: 50,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 
    {
      left: 319,
      top: 165,
      width: 54,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 
    {
      left: 372,
      top: 165,
      width: 65,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 
    {
      left: 438,
      top: 165,
      width: 63,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 
    {
      left: 18,
      top: 165,
      width: 46,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 
    {
      left: 75,
      top: 165,
      width: 52,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 
    ],


    this.runnerManCellsLeft = [
    {
      left: 67,
      top: 245,
      width: 58,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 
    {
      left: 133,
      top: 245,
      width: 54,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 
    {
      left: 196,
      top: 245,
      width: 50,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 
    {
      left: 255,
      top: 245,
      width: 57,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 
    {
      left: 317,
      top: 245,
      width: 62,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 
    {
      left: 380,
      top: 245,
      width: 52,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 
    {
      left: 445,
      top: 245,
      width: 46,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 
    {
      left: 4,
      top: 245,
      width: 55,
      height: magicNumbers.RUNNER_CELLS_HEIGHT
    }, 

    ];


  // оранжевая звезда..................................................
  this.orangeStarCells = [{
    left: 9,
    top: 340,
    width: 43,
    height: magicNumbers.ORANGE_STAR_CELLS_HEIGHT
  }, {
    left: 52,
    top: 340,
    width: 34,
    height: magicNumbers.ORANGE_STAR_CELLS_HEIGHT
  }, {
    left: 90,
    top: 340,
    width: 23,
    height: magicNumbers.ORANGE_STAR_CELLS_HEIGHT
  }, {
    left: 113,
    top: 340,
    width: 28,
    height: magicNumbers.ORANGE_STAR_CELLS_HEIGHT
  }, {
    left: 139,
    top: 340,
    width: 43,
    height: magicNumbers.ORANGE_STAR_CELLS_HEIGHT
  }, {
    left: 183,
    top: 340,
    width: 28,
    height: magicNumbers.ORANGE_STAR_CELLS_HEIGHT
  }, {
    left: 210,
    top: 340,
    width: 25,
    height: magicNumbers.ORANGE_STAR_CELLS_HEIGHT
  }, {
    left: 236,
    top: 340,
    width: 35,
    height: magicNumbers.ORANGE_STAR_CELLS_HEIGHT
  }, ];

  // красная звезда..................................................
  this.redStarCells = [{
    left: 12,
    top: 391,
    width: 44,
    height: magicNumbers.RED_STAR_CELLS_HEIGHT
  }, {
    left: 57,
    top: 391,
    width: 39,
    height: magicNumbers.RED_STAR_CELLS_HEIGHT
  }, {
    left: 98,
    top: 391,
    width: 22,
    height: magicNumbers.RED_STAR_CELLS_HEIGHT
  }, {
    left: 123,
    top: 391,
    width: 29,
    height: magicNumbers.RED_STAR_CELLS_HEIGHT
  }, {
    left: 154,
    top: 391,
    width: 41,
    height: magicNumbers.RED_STAR_CELLS_HEIGHT
  }, {
    left: 197,
    top: 391,
    width: 30,
    height: magicNumbers.RED_STAR_CELLS_HEIGHT
  }, {
    left: 227,
    top: 391,
    width: 25,
    height: magicNumbers.RED_STAR_CELLS_HEIGHT
  }, {
    left: 253,
    top: 391,
    width: 38,
    height: magicNumbers.RED_STAR_CELLS_HEIGHT
  }, ];

  // сюрикены..........................................................................
  this.orangeSuricaneCells = [{
    left: 10,
    top: 682,
    width: magicNumbers.ORANGE_SURICANE_CELLS_WIDTH,
    height: magicNumbers.ORANGE_SURICANE_CELLS_HEIGHT
  }, {
    left: 56,
    top: 682,
    width: magicNumbers.ORANGE_SURICANE_CELLS_WIDTH,
    height: magicNumbers.ORANGE_SURICANE_CELLS_HEIGHT
  }, {
    left: 105,
    top: 682,
    width: magicNumbers.ORANGE_SURICANE_CELLS_WIDTH,
    height: magicNumbers.ORANGE_SURICANE_CELLS_HEIGHT
  }, {
    left: 154,
    top: 682,
    width: magicNumbers.ORANGE_SURICANE_CELLS_WIDTH,
    height: magicNumbers.ORANGE_SURICANE_CELLS_HEIGHT
  }, {
    left: 203,
    top: 682,
    width: magicNumbers.ORANGE_SURICANE_CELLS_WIDTH,
    height: magicNumbers.ORANGE_SURICANE_CELLS_HEIGHT
  }, {
    left: 255,
    top: 682,
    width: magicNumbers.ORANGE_SURICANE_CELLS_WIDTH,
    height: magicNumbers.ORANGE_SURICANE_CELLS_HEIGHT
  }, {
    left: 304,
    top: 682,
    width: magicNumbers.ORANGE_SURICANE_CELLS_WIDTH,
    height: magicNumbers.ORANGE_SURICANE_CELLS_HEIGHT
  }, {
    left: 351,
    top: 682,
    width: magicNumbers.ORANGE_SURICANE_CELLS_WIDTH,
    height: magicNumbers.ORANGE_SURICANE_CELLS_HEIGHT
  }, {
    left: 396,
    top: 682,
    width: magicNumbers.ORANGE_SURICANE_CELLS_WIDTH,
    height: magicNumbers.ORANGE_SURICANE_CELLS_HEIGHT
  }, {
    left: 442,
    top: 682,
    width: magicNumbers.ORANGE_SURICANE_CELLS_WIDTH,
    height: magicNumbers.ORANGE_SURICANE_CELLS_HEIGHT
  }, {
    left: 495,
    top: 682,
    width: magicNumbers.ORANGE_SURICANE_CELLS_WIDTH,
    height: magicNumbers.ORANGE_SURICANE_CELLS_HEIGHT
  }, ];

  this.redSuricaneCells = [{
    left: 7,
    top: 728,
    width: magicNumbers.RED_SURICANE_CELLS_WIDTH,
    height: magicNumbers.RED_SURICANE_CELLS_HEIGHT
  }, {
    left: 56,
    top: 682,
    width: magicNumbers.RED_SURICANE_CELLS_WIDTH,
    height: magicNumbers.RED_SURICANE_CELLS_HEIGHT
  }, {
    left: 105,
    top: 682,
    width: magicNumbers.RED_SURICANE_CELLS_WIDTH,
    height: magicNumbers.RED_SURICANE_CELLS_HEIGHT
  }, {
    left: 154,
    top: 682,
    width: magicNumbers.RED_SURICANE_CELLS_WIDTH,
    height: magicNumbers.RED_SURICANE_CELLS_HEIGHT
  }, {
    left: 203,
    top: 682,
    width: magicNumbers.RED_SURICANE_CELLS_WIDTH,
    height: magicNumbers.RED_SURICANE_CELLS_HEIGHT
  }, {
    left: 255,
    top: 682,
    width: magicNumbers.RED_SURICANE_CELLS_WIDTH,
    height: magicNumbers.RED_SURICANE_CELLS_HEIGHT
  }, {
    left: 304,
    top: 682,
    width: magicNumbers.RED_SURICANE_CELLS_WIDTH,
    height: magicNumbers.RED_SURICANE_CELLS_HEIGHT
  }, {
    left: 351,
    top: 682,
    width: magicNumbers.RED_SURICANE_CELLS_WIDTH,
    height: magicNumbers.RED_SURICANE_CELLS_HEIGHT
  }, {
    left: 396,
    top: 682,
    width: magicNumbers.RED_SURICANE_CELLS_WIDTH,
    height: magicNumbers.RED_SURICANE_CELLS_HEIGHT
  }, {
    left: 442,
    top: 682,
    width: magicNumbers.RED_SURICANE_CELLS_WIDTH,
    height: magicNumbers.RED_SURICANE_CELLS_HEIGHT
  }, {
    left: 495,
    top: 682,
    width: magicNumbers.RED_SURICANE_CELLS_WIDTH,
    height: magicNumbers.RED_SURICANE_CELLS_HEIGHT
  }, ];


  // тучка............................................................
  this.cloudCells = [{
      left: 13,
      top: 447,
      width: 48,
      height: 48
    }, {
      left: 66,
      top: 449,
      width: 48,
      height: 30
    }, {
      left: 121,
      top: 449,
      width: 48,
      height: 30
    }, {
      left: 173,
      top: 449,
      width: 48,
      height: 30
    },

  ];

  // старик............................................................
  this.oldManCellsRight = [{
    left: 12,
    top: 509,
    width: 36,
    height: magicNumbers.OLD_MAN_CELLS_HEIGHT
  }, {
    left: 48,
    top: 509,
    width: 33,
    height: magicNumbers.OLD_MAN_CELLS_HEIGHT
  }, {
    left: 84,
    top: 509,
    width: 36,
    height: magicNumbers.OLD_MAN_CELLS_HEIGHT
  }, {
    left: 123,
    top: 509,
    width: 36,
    height: magicNumbers.OLD_MAN_CELLS_HEIGHT
  }, ];

  this.oldManCellsLeft = [{
    left: 13,
    top: 576,
    width: 34,
    height: magicNumbers.OLD_MAN_CELLS_HEIGHT
  }, {
    left: 48,
    top: 576,
    width: 36,
    height: magicNumbers.OLD_MAN_CELLS_HEIGHT
  }, {
    left: 85,
    top: 576,
    width: 33,
    height: magicNumbers.OLD_MAN_CELLS_HEIGHT
  }, {
    left: 120,
    top: 576,
    width: 36,
    height: magicNumbers.OLD_MAN_CELLS_HEIGHT
  }, ];

  // песок.............................................................
  this.sandCells = [{
    left: 19,
    top: 656,
    width: 30,
    height: 14
  }, {
    left: 45,
    top: 659,
    width: 32,
    height: 13
  }, {
    left: 80,
    top: 651,
    width: 29,
    height: 20
  }, ];
}
