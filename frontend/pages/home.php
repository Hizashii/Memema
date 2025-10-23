<?php
// Data later should come from the DB this is just example
$news = [
  [
    'img' => './assets/img/OutsideCinema.jpg',
    'title' => 'Local Film Festival Kicks Off',
    'excerpt' => 'The annual city film festival began last night…',
    'url' => '#',
  ],
  [
    'img' => './assets/img/AbsoluteRobotAi.jpg',
    'title' => 'Director on AI & Cinema',
    'excerpt' => 'Renowned director Maria Chen shared her insights…',
    'url' => '#',
  ],
  [
    'img' => './assets/img/movieMarathon.png',
    'title' => 'Classic Movie Marathon',
    'excerpt' => 'Get ready for a nostalgic trip as CinemaBook announces…',
    'url' => '#',
  ],
];

$shows = [
  [
    'title' => 'Oppenheimer',
    'img' => './assets/img/Oppenheimer.jpg',
    'tag' => ['Drama','bg-cyan-600/90'],
    'times' => ['10:00 AM','01:30 PM','04:00 PM'],
  ],
  [
    'title' => 'The Dark Knight',
    'img' => './assets/img/TheDarkKnight.jpg',
    'tag' => ['Action','bg-emerald-600/90'],
    'times' => ['11:00 AM','02:15 PM','05:00 PM'],
  ],
  [
    'title' => 'Free Guy',
    'img' => './assets/img/FreeGuy.jpg',
    'tag' => ['Comedy','bg-emerald-600/90'],
    'times' => ['10:30 AM','01:00 PM','03:45 PM'],
  ],
  [
    'title' => 'Joker',
    'img' => './assets/img/Joker.png',
    'tag' => ['Drama','bg-teal-600/90'],
    'times' => ['12:00 PM','02:45 PM','05:30 PM'],
  ],
];
?>

<main class="space-y-16">

  <section class="relative mt-20 h-[60vh] md:h-[75vh] rounded-xl overflow-hidden">
    <img src="./assets/img/cinemaMan.jpg" alt="Cinema hero"
         class="absolute inset-0 w-full h-full object-cover object-[center_30%]">
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-black/10"></div>

    <div class="relative z-10 h-full flex flex-col items-center justify-center text-center text-white px-6">
      <h1 class="text-3xl md:text-6xl font-extrabold">ABSOLUTE CINEMA</h1>
      <a href="/Cinema/frontend/pages/movies.php"
         class="mt-5 inline-block bg-purple-700 hover:bg-purple-800 text-white px-5 py-2 rounded-md">
        Explore Movies Now
      </a>
    </div>
  </section>

  <section class="max-w-6xl mx-auto px-4">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-bold">Latest News &amp; Updates</h2>
    </div>

    <div class="mt-6 grid gap-6 md:grid-cols-3">
      <?php foreach ($news as $n): ?>
        <article class="bg-white rounded-xl border shadow-sm overflow-hidden flex flex-col h-full">
          <div class="h-40 bg-gray-200">
            <img src="<?= htmlspecialchars($n['img']) ?>" alt=""
                 class="w-full h-full object-cover">
          </div>
          <div class="p-4 flex flex-col">
            <h3 class="font-semibold"><?= htmlspecialchars($n['title']) ?></h3>
            <p class="mt-1 text-gray-700 text-sm"><?= htmlspecialchars($n['excerpt']) ?></p>
            <a href="<?= htmlspecialchars($n['url']) ?>" class="mt-3 text-purple-700 hover:text-purple-800 text-sm">Read more</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <div class="mt-8 text-center">
      <a href="/Cinema/frontend/pages/news.php"
         class="inline-block border px-4 py-2 rounded-md hover:bg-gray-50">
        View All News
      </a>
    </div>
  </section>

  <section class="max-w-6xl mx-auto px-4">
    <h2 class="text-2xl font-bold text-center">Daily Showtimes</h2>

    <div class="mt-6 grid gap-6 md:grid-cols-4">
      <?php foreach ($shows as $s): [$tagText,$tagClr] = $s['tag']; ?>
        <div class="group relative rounded-xl shadow-lg overflow-hidden bg-neutral-900 text-white">
          <div class="relative h-80">
            <img src="<?= htmlspecialchars($s['img']) ?>" alt="<?= htmlspecialchars($s['title']) ?>"
                 class="absolute inset-0 w-full h-full object-cover transition duration-300 group-hover:brightness-50 group-hover:scale-105">
          </div>
          <div class="pointer-events-none absolute inset-0 p-4 flex flex-col justify-between opacity-0 translate-y-2 transition duration-300 group-hover:opacity-100 group-hover:translate-y-0">
            <div class="flex items-center justify-between text-xs">
              <span class="font-medium drop-shadow"><?= htmlspecialchars($s['title']) ?></span>
              <span class="<?= $tagClr ?> text-white px-2 py-0.5 rounded-full"><?= htmlspecialchars($tagText) ?></span>
            </div>
            <div>
              <div class="flex flex-wrap gap-2 text-sm">
                <?php foreach ($s['times'] as $t): ?>
                  <span class="px-2 py-1 rounded-md bg-white/15 border border-white/20 backdrop-blur"><?= htmlspecialchars($t) ?></span>
                <?php endforeach; ?>
              </div>
              <a href="/Cinema/frontend/pages/booking.php"
                 class="mt-4 block w-full text-center bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-md pointer-events-auto">
                Book Now
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

</main>
