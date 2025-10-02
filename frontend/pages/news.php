<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<?php
// Need to replace later with SQL

$featured = [
  'title' => "Blockbuster Season Kicks Off with Record-Breaking Openings",
  'img'   => "../assets/img/robots.jpg",
  'date'  => "July 15, 2024",
  'text'  => "The summer blockbuster season has officially begun, and it’s already shattering records.
              Analysts predict a lucrative quarter ahead, driven by highly anticipated sequels and original hits.",
  'link'  => "#"
];

$popular = [
  ["title"=>"Directors Discuss the Future of VR Cinema","date"=>"July 12, 2024"],
  ["title"=>"New Talent: Five Actors to Watch This Year","date"=>"July 11, 2024"],
  ["title"=>"Film Festivals Announce Fall Lineups","date"=>"July 09, 2024"],
  ["title"=>"The Impact of AI on Screenwriting","date"=>"July 07, 2024"],
  ["title"=>"From Page to Screen: Adapting Best-Selling Novels","date"=>"July 05, 2024"],
];

$articles = [
  [
    'title'=>"Sci-Fi Epic ‘Stellaris’ Dominates International Box Office",
    'img'=>"../assets/img/spaceShip.jpg",
    'date'=>"July 14, 2024",
    'text'=>"Director Arya Sharma’s latest sci-fi masterpiece has taken the international box office by storm…",
  ],
  [
    'title'=>"Historical Drama ‘The Last Reign’ Receives Oscar Buzz",
    'img'=>"../assets/img/blackWhiteBar.jpg",
    'date'=>"July 12, 2024",
    'text'=>"Critics are raving about the sweeping historical drama, praising its authentic performances…",
  ],
  [
    'title'=>"Behind the Scenes: The Making of ‘Echoes of Tomorrow’",
    'img'=>"../assets/img/friendsLaugh.jpg",
    'date'=>"July 10, 2024",
    'text'=>"An exclusive look at the intricate process of bringing the film to life…",
  ],
  [
    'title'=>"Romantic Comedy ‘First Date Fiasco’ Charms Audiences",
    'img'=>"../assets/img/coupleSunset.jpg",
    'date'=>"July 08, 2024",
    'text'=>"A delightful new romantic comedy with a witty script and lovable leads…",
  ],
  [
    'title'=>"Horror Film ‘The Whispering Woods’ Sets New Fright",
    'img'=>"../assets/img/moonHouse.jpg",
    'date'=>"July 06, 2024",
    'text'=>"The latest entry in the horror genre is being hailed as a terrifying masterpiece…",
  ],
  [
    'title'=>"Animated Feature ‘Journey to Eldoria’ Breaks Family Film Records",
    'img'=>"../assets/img/animatorsDesk.jpg",
    'date'=>"July 04, 2024",
    'text'=>"The enchanting animated adventure has captivated families worldwide…",
  ],
];
?>

<main class="max-w-6xl mx-auto px-4 py-10 space-y-12">

  <h1 class="text-3xl md:text-4xl font-extrabold text-center text-purple-700">
    Latest Cinematic News &amp; Updates
  </h1>

  <section class="grid gap-6 md:grid-cols-3">
    <article class="md:col-span-2 bg-white rounded-xl border shadow-sm overflow-hidden">
      <div class="aspect-[16/9]">
        <img src="<?= $featured['img'] ?>" alt="Featured story" class="w-full h-full object-cover">
      </div>
      <div class="p-5 space-y-3">
        <h2 class="text-xl md:text-2xl font-bold"><?= $featured['title'] ?></h2>
        <div class="text-xs text-gray-500"><?= $featured['date'] ?></div>
        <p class="text-gray-700"><?= $featured['text'] ?></p>
        <a href="<?= $featured['link'] ?>" class="inline-block text-purple-700 hover:text-purple-800 text-sm">Read More</a>
      </div>
    </article>

    <aside class="bg-white rounded-xl border shadow-sm p-4">
      <h3 class="font-semibold mb-3">Popular News</h3>
      <ul class="divide-y">
        <?php foreach ($popular as $p): ?>
          <li class="py-3">
            <a href="#" class="block">
              <div class="text-sm font-medium"><?= htmlspecialchars($p['title']) ?></div>
              <div class="text-xs text-gray-500"><?= htmlspecialchars($p['date']) ?></div>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
      <a href="#" class="mt-3 inline-block text-purple-700 hover:text-purple-800 text-sm">More Popular Stories</a>
    </aside>
  </section>

  <section class="space-y-4">
    <h2 class="text-xl font-bold">Recent Articles</h2>
    <div class="grid gap-6 md:grid-cols-3">
      <?php foreach ($articles as $a): ?>
        <article class="bg-white rounded-xl border shadow-sm overflow-hidden flex flex-col">
          <div class="aspect-[16/10]">
            <img src="<?= $a['img'] ?>" alt="<?= htmlspecialchars($a['title']) ?>" class="w-full h-full object-cover">
          </div>
          <div class="p-4 space-y-3">
            <h3 class="font-semibold"><?= htmlspecialchars($a['title']) ?></h3>
            <div class="text-xs text-gray-500"><?= $a['date'] ?></div>
            <p class="text-sm text-gray-700"><?= htmlspecialchars($a['text']) ?></p>
            <a href="#" class="inline-block text-purple-700 hover:text-purple-800 text-sm">Read More</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

</main>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
