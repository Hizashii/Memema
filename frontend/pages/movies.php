<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<?php
// Need to replace later with SQL
$movies = [
  [
    'title' => "Cosmic Odyssey",
    'img'   => "../assets/img/spaceShip.jpg",
    'dur'   => "2h 15m",
    'genres'=> ["Sci-Fi","Action"],
    'rating'=> "4.7",
    'venue' => "Downtown Cinema",
  ],
  [
    'title' => "Laugh Riot",
    'img'   => "../assets/img/friendsLaugh.jpg",
    'dur'   => "1h 45m",
    'genres'=> ["Comedy"],
    'rating'=> "4.2",
    'venue' => "Grand Plex",
  ],
  [
    'title' => "The Last Whisper",
    'img'   => "../assets/img/blackWhiteBar.jpg",
    'dur'   => "2h 05m",
    'genres'=> ["Thriller","Drama"],
    'rating'=> "4.5",
    'venue' => "Sunset Drive-In",
  ],
  [
    'title' => "Dragon's Quest",
    'img'   => "../assets/img/dragon.jpg",
    'dur'   => "2h 30m",
    'genres'=> ["Fantasy","Action"],
    'rating'=> "4.8",
    'venue' => "Majestic Theaters",
  ],
  [
    'title' => "Echoes of Love",
    'img'   => "../assets/img/coupleSunset.jpg",
    'dur'   => "1h 50m",
    'genres'=> ["Romance","Drama"],
    'rating'=> "4.1",
    'venue' => "Downtown Cinema",
  ],
  [
    'title' => "Midnight Haunt",
    'img'   => "../assets/img/moonHouse.jpg",
    'dur'   => "1h 35m",
    'genres'=> ["Horror"],
    'rating'=> "3.9",
    'venue' => "Grand Plex",
  ],
  [
    'title' => "Robot Revolution",
    'img'   => "../assets/img/robots.jpg",
    'dur'   => "2h 10m",
    'genres'=> ["Sci-Fi","Action"],
    'rating'=> "4.5",
    'venue' => "Sunset Drive-In",
  ],
  [
    'title' => "The Animator's Dream",
    'img'   => "../assets/img/animatorsDesk.jpg",
    'dur'   => "1h 20m",
    'genres'=> ["Animation","Family"],
    'rating'=> "4.4",
    'venue' => "Majestic Theaters",
  ],
  [
    'title' => "Historic Heist",
    'img'   => "../assets/img/galleryHeist.jpg",
    'dur'   => "1h 51m",
    'genres'=> ["Action","Thriller"],
    'rating'=> "4.3",
    'venue' => "Downtown Cinema",
  ],
];

function genre_pill_classes($g) {
  return match (strtolower($g)) {
    'sci-fi'  => 'bg-emerald-100 text-emerald-700',
    'action'           => 'bg-blue-100 text-blue-700',
    'thriller'         => 'bg-teal-100 text-teal-700',
    'drama'            => 'bg-cyan-100 text-cyan-700',
    'fantasy'          => 'bg-sky-100 text-sky-700',
    'romance'          => 'bg-pink-100 text-pink-700',
    'horror'           => 'bg-red-100 text-red-700',
    'animation'        => 'bg-amber-100 text-amber-700',
    'family'           => 'bg-green-100 text-green-700',
    default            => 'bg-gray-100 text-gray-700',
  };
}
?>

<main class="max-w-6xl mx-auto px-4 py-8 space-y-8">
  <h1 class="text-2xl font-bold">Movies &amp; Venues</h1>

  <div class="rounded-xl border bg-white shadow-sm p-3">
    <div class="flex flex-wrap gap-2">
      <?php
        $filters = [
          'Genre: <span class="font-semibold">All</span>',
          'Showing Date',
          'Venue: <span class="font-semibold">All</span>',
          'Language: <span class="font-semibold">All</span>',
          'Rating: <span class="font-semibold">All</span>',
        ];
        foreach ($filters as $f): ?>
          <button class="inline-flex items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-gray-50">
            <?= $f ?>
          </button>
      <?php endforeach; ?>
      <button class="ml-auto inline-flex items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-gray-50">
        Sort by: <span class="font-semibold">Popularity</span>
      </button>
      <button class="inline-flex items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-gray-50">✕ Clear All</button>
    </div>
  </div>

  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
    <?php foreach ($movies as $m): ?>
      <article class="bg-white rounded-xl border shadow-sm overflow-hidden flex flex-col">
        <div class="aspect-[16/10] bg-gray-200">
          <img src="<?= htmlspecialchars($m['img']) ?>" alt="<?= htmlspecialchars($m['title']) ?>" class="w-full h-full object-cover">
        </div>
        <div class="p-4 flex flex-col gap-3">
          <div>
            <h3 class="font-semibold"><?= htmlspecialchars($m['title']) ?></h3>
            <div class="text-xs text-gray-600"><?= htmlspecialchars($m['dur']) ?></div>
          </div>

          <div class="flex flex-wrap gap-2">
            <?php foreach ($m['genres'] as $g): ?>
              <span class="text-xs rounded-full px-2 py-0.5 <?= genre_pill_classes($g) ?>">
                <?= htmlspecialchars($g) ?>
              </span>
            <?php endforeach; ?>
          </div>

          <div class="text-xs text-gray-600 flex items-center gap-2">
            ⭐ <?= htmlspecialchars($m['rating']) ?> <span class="text-gray-400">|</span> <?= htmlspecialchars($m['venue']) ?>
          </div>

          <a href="/Cinema/frontend/pages/booking.php"
             class="mt-1 inline-block text-center bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-md">
            Book Now
          </a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</main>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
