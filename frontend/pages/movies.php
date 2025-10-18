<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<?php
// Need to replace later with SQL
$movies = [
  [
    'title' => "Oppenheimer",
    'img'   => "../assets/img/Oppenheimer.jpg",
    'dur'   => "3h 0m",
    'genres'=> ["Drama","Biography"],
    'rating'=> "4.8",
    'venue' => "Downtown Cinema",
  ],
  [
    'title' => "The Dark Knight",
    'img'   => "../assets/img/TheDarkKnight.jpg",
    'dur'   => "2h 32m",
    'genres'=> ["Action","Crime"],
    'rating'=> "4.9",
    'venue' => "Grand Plex",
  ],
  [
    'title' => "Joker",
    'img'   => "../assets/img/Joker.png",
    'dur'   => "2h 2m",
    'genres'=> ["Drama","Crime"],
    'rating'=> "4.6",
    'venue' => "Sunset Drive-In",
  ],
  [
    'title' => "The Godfather",
    'img'   => "../assets/img/TheGodFather.jpg",
    'dur'   => "2h 55m",
    'genres'=> ["Crime","Drama"],
    'rating'=> "4.9",
    'venue' => "Majestic Theaters",
  ],
  [
    'title' => "Pulp Fiction",
    'img'   => "../assets/img/PulpFiction.jpg",
    'dur'   => "2h 34m",
    'genres'=> ["Crime","Drama"],
    'rating'=> "4.7",
    'venue' => "Downtown Cinema",
  ],
  [
    'title' => "Alien",
    'img'   => "../assets/img/Alien.jpg",
    'dur'   => "1h 57m",
    'genres'=> ["Horror","Sci-Fi"],
    'rating'=> "4.5",
    'venue' => "Grand Plex",
  ],
  [
    'title' => "Free Guy",
    'img'   => "../assets/img/FreeGuy.jpg",
    'dur'   => "1h 55m",
    'genres'=> ["Comedy","Action"],
    'rating'=> "4.2",
    'venue' => "Sunset Drive-In",
  ],
  [
    'title' => "Brave",
    'img'   => "../assets/img/Brave.jpg",
    'dur'   => "1h 33m",
    'genres'=> ["Animation","Family"],
    'rating'=> "4.1",
    'venue' => "Majestic Theaters",
  ],
  [
    'title' => "7 Samurai",
    'img'   => "../assets/img/7Samurai.jpg",
    'dur'   => "3h 27m",
    'genres'=> ["Drama","Action"],
    'rating'=> "4.8",
    'venue' => "Downtown Cinema",
  ],
  [
    'title' => "The Power of the Dog",
    'img'   => "../assets/img/ThePowerOfTheDog.jpg",
    'dur'   => "2h 6m",
    'genres'=> ["Drama","Western"],
    'rating'=> "4.3",
    'venue' => "Grand Plex",
  ],
  [
    'title' => "Wreck-It Ralph",
    'img'   => "../assets/img/WreckItRalph.jpg",
    'dur'   => "1h 41m",
    'genres'=> ["Animation","Comedy"],
    'rating'=> "4.0",
    'venue' => "Sunset Drive-In",
  ],
  [
    'title' => "The Lorax",
    'img'   => "../assets/img/TheLorax.jpg",
    'dur'   => "1h 26m",
    'genres'=> ["Animation","Family"],
    'rating'=> "3.8",
    'venue' => "Majestic Theaters",
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
    'biography'        => 'bg-purple-100 text-purple-700',
    'crime'            => 'bg-gray-100 text-gray-700',
    'comedy'           => 'bg-yellow-100 text-yellow-700',
    'western'          => 'bg-orange-100 text-orange-700',
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
