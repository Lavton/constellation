var magicNumbers = {
  RUNNER_CELLS_HEIGHT: 60, // высота бегуна
  STARTING_RUNNER_LEFT: 100, // начальное положение бегуна
  RUNNER_JUMP_DURATION: 1500, // время прыжка
  RUNNER_JUMP_HEIGHT: 120, // высота прыжка в пикселях

  ORANGE_STAR_CELLS_HEIGHT: 38, // высота оранжевой звезды
  RED_STAR_CELLS_HEIGHT: 41, // высота красной звезды
  ORANGE_SURICANE_CELLS_HEIGHT: 25, // высота оранжевого сюрикена
  ORANGE_SURICANE_CELLS_WIDTH: 39, // ширина оранжевого сюрикена

  RED_SURICANE_CELLS_HEIGHT: 25, // высота красного сюрикена
  RED_SURICANE_CELLS_WIDTH: 39, // ширина красного сюрикена

  CLOUD_CELLS_HEIGHT: 48, // высота (максимальная) облака
  OLD_MAN_CELLS_HEIGHT: 60, // высота старика

  PLATFORM_HEIGHT: 8, // высота платформ

  TRACK_1_BASELINE: 323, // расположение 1 уровня
  TRACK_2_BASELINE: 223, // расположение 2 уровня
  TRACK_3_BASELINE: 123, // расположение 3 уровня

  RUN_ANIMATION_RATE: 7, // частота бега бегуна
  OLDMAN_PACE_VELOCITY: 50, // скорость "снувания" старика
  SAND_VALOCITY: 150, // скорость распространения песка

  SURICANE_VELOCITY:300, // скорость стрельбы сюрикена

  pegging: { // привязка изображения спрайте к левому(верху)/центру/правому(низу)
    TOP: 1,
    CENTER: 2,
    BUTTON: 3,
  },

  direction: { // направление передвижения
    LEFT: 1,
    RIGHT: 2,
  },

  now_going: { // показатель, что кнопка движения влево/вправо зажата
    LEFT: 1,
    RIGHT: 2,
    NOWHERE: 3
  }
}
