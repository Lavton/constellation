var dataFactory = function() {
    // оранжевая звезда........................................................
    this.orangeStarData = [
    {
      left: 20,
      top: 223
    },
    {
      left: 500,
      top: 64
    }, 
    {
      left: 944,
      top: magicNumbers.TRACK_2_BASELINE - magicNumbers.ORANGE_STAR_CELLS_HEIGHT - 30
    }, {
      left: 1600,
      top: 125
    }, {
      left: 2225,
      top: 125
    }, {
      left: 2295,
      top: 275
    }, {
      left: 2450,
      top: 275
    }, 

    ];

// старик
  this.oldManData = [
      { platformIndex: 1 },
  ];

// тучка
  this.cloudData = [
      { left: 828,
      top: 165 },
  ];


// красная звезда
  this.redStarData = [
      { left: 1245,
      top: 77 },
  ];
}