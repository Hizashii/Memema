<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<?php
// Sample venue data (would come from database)
$venues = [
  [
    'id' => 1,
    'name' => 'Downtown Cinema',
    'address' => '123 Main Street, Downtown, DC 12345',
    'phone' => '+1 (555) 123-4567',
    'image' => '../assets/img/OutsideCinema.jpg',
    'description' => 'Our flagship location in the heart of downtown with 8 screens and premium amenities.',
    'amenities' => ['IMAX', 'Dolby Atmos', 'Reclining Seats', 'Bar & Lounge', 'Free WiFi', 'Parking'],
    'screens' => 8,
    'capacity' => 1200,
    'features' => ['Premium Sound', '4K Projection', 'Accessible Seating'],
    'hours' => '10:00 AM - 11:00 PM Daily',
    'parking' => 'Free parking available',
    'transit' => 'Metro: Downtown Station (2 min walk)'
  ],
  [
    'id' => 2,
    'name' => 'Grand Plex',
    'address' => '456 Entertainment Blvd, Midtown, MP 23456',
    'phone' => '+1 (555) 234-5678',
    'image' => '../assets/img/cinemaMan.jpg',
    'description' => 'A modern multiplex featuring the latest in cinema technology and comfort.',
    'amenities' => ['3D Capable', 'Premium Seating', 'Concession Stand', 'Arcade', 'Party Rooms'],
    'screens' => 12,
    'capacity' => 1800,
    'features' => ['Laser Projection', 'Surround Sound', 'Wheelchair Accessible'],
    'hours' => '9:00 AM - 12:00 AM Daily',
    'parking' => 'Valet parking available',
    'transit' => 'Bus: Lines 15, 23, 45'
  ],
  [
    'id' => 3,
    'name' => 'Sunset Drive-In',
    'address' => '789 Sunset Highway, Suburbia, SH 34567',
    'phone' => '+1 (555) 345-6789',
    'image' => '../assets/img/movieMarathon.png',
    'description' => 'Experience the nostalgia of drive-in cinema with modern amenities and comfort.',
    'amenities' => ['Drive-In Experience', 'FM Radio Sound', 'Concession Stand', 'Restrooms', 'Pet Friendly'],
    'screens' => 2,
    'capacity' => 400,
    'features' => ['Outdoor Seating', 'Family Friendly', 'Weather Protection'],
    'hours' => '7:00 PM - 11:00 PM (Seasonal)',
    'parking' => 'Car parking spaces',
    'transit' => 'Car recommended'
  ],
  [
    'id' => 4,
    'name' => 'Majestic Theaters',
    'address' => '321 Historic Avenue, Old Town, OT 45678',
    'phone' => '+1 (555) 456-7890',
    'image' => '../assets/img/warsStar.jpg',
    'description' => 'A beautifully restored historic theater offering classic and independent films.',
    'amenities' => ['Historic Architecture', 'Art House Films', 'Wine Bar', 'Live Events', 'Private Screenings'],
    'screens' => 3,
    'capacity' => 450,
    'features' => ['Original Design', 'Intimate Setting', 'Cultural Events'],
    'hours' => '2:00 PM - 10:00 PM (Tue-Sun)',
    'parking' => 'Street parking available',
    'transit' => 'Walking distance from Old Town Station'
  ]
];

// Sample current movies for each venue
$currentMovies = [
  'Downtown Cinema' => ['Oppenheimer', 'The Godfather', 'Pulp Fiction', '7 Samurai'],
  'Grand Plex' => ['The Dark Knight', 'Alien', 'The Power of the Dog', 'Free Guy'],
  'Sunset Drive-In' => ['Joker', 'Free Guy', 'Wreck-It Ralph'],
  'Majestic Theaters' => ['Brave', 'The Lorax', 'The Godfather']
];

function getAmenityIcon($amenity) {
  $icons = [
    'IMAX' => '<i class="fas fa-film text-purple-600"></i>',
    'Dolby Atmos' => '<i class="fas fa-volume-up text-blue-600"></i>',
    'Reclining Seats' => '<i class="fas fa-chair text-green-600"></i>',
    'Bar & Lounge' => '<i class="fas fa-cocktail text-amber-600"></i>',
    'Free WiFi' => '<i class="fas fa-wifi text-blue-600"></i>',
    'Parking' => '<i class="fas fa-parking text-gray-600"></i>',
    '3D Capable' => '<i class="fas fa-glasses text-purple-600"></i>',
    'Premium Seating' => '<i class="fas fa-couch text-indigo-600"></i>',
    'Concession Stand' => '<i class="fas fa-popcorn text-yellow-600"></i>',
    'Arcade' => '<i class="fas fa-gamepad text-pink-600"></i>',
    'Party Rooms' => '<i class="fas fa-birthday-cake text-red-600"></i>',
    'Drive-In Experience' => '<i class="fas fa-car text-blue-600"></i>',
    'FM Radio Sound' => '<i class="fas fa-radio text-orange-600"></i>',
    'Restrooms' => '<i class="fas fa-restroom text-gray-600"></i>',
    'Pet Friendly' => '<i class="fas fa-paw text-green-600"></i>',
    'Historic Architecture' => '<i class="fas fa-landmark text-amber-600"></i>',
    'Art House Films' => '<i class="fas fa-palette text-purple-600"></i>',
    'Wine Bar' => '<i class="fas fa-wine-glass text-red-600"></i>',
    'Live Events' => '<i class="fas fa-microphone text-pink-600"></i>',
    'Private Screenings' => '<i class="fas fa-lock text-gray-600"></i>'
  ];
  return $icons[$amenity] ?? '<i class="fas fa-star text-yellow-500"></i>';
}
?>

<main class="max-w-7xl mx-auto px-4 py-10 space-y-12">

  <!-- Hero Section -->
  <section class="text-center">
    <h1 class="text-4xl font-extrabold mb-4">Our Venues</h1>
    <p class="text-lg text-gray-600 max-w-3xl mx-auto">
      Discover our diverse collection of cinema venues, each offering a unique movie-going experience with state-of-the-art technology and premium amenities.
    </p>
  </section>

  <!-- Venue Filter -->
  <section class="rounded-xl border bg-white shadow-sm p-6">
    <div class="flex flex-wrap gap-3 items-center">
      <span class="font-semibold">Filter by:</span>
      <button class="venue-filter px-4 py-2 rounded-md border hover:bg-gray-50 active" data-filter="all">
        All Venues
      </button>
      <button class="venue-filter px-4 py-2 rounded-md border hover:bg-gray-50" data-filter="downtown">
        Downtown
      </button>
      <button class="venue-filter px-4 py-2 rounded-md border hover:bg-gray-50" data-filter="multiplex">
        Multiplex
      </button>
      <button class="venue-filter px-4 py-2 rounded-md border hover:bg-gray-50" data-filter="drive-in">
        Drive-In
      </button>
      <button class="venue-filter px-4 py-2 rounded-md border hover:bg-gray-50" data-filter="historic">
        Historic
      </button>
    </div>
  </section>

  <!-- Venues Grid -->
  <section class="grid gap-8 lg:grid-cols-2">
    <?php foreach ($venues as $venue): ?>
      <div class="venue-card rounded-xl border bg-white shadow-sm overflow-hidden" 
           data-venue="<?= strtolower(str_replace(' ', '-', $venue['name'])) ?>">
        
        <!-- Venue Image -->
        <div class="aspect-video bg-gray-200">
          <img src="<?= htmlspecialchars($venue['image']) ?>" 
               alt="<?= htmlspecialchars($venue['name']) ?>" 
               class="w-full h-full object-cover">
        </div>
        
        <div class="p-6 space-y-4">
          <!-- Venue Header -->
          <div>
            <h3 class="text-2xl font-bold mb-2"><?= htmlspecialchars($venue['name']) ?></h3>
            <p class="text-gray-600"><?= htmlspecialchars($venue['description']) ?></p>
          </div>
          
          <!-- Venue Info -->
          <div class="grid gap-4 md:grid-cols-2 text-sm">
            <div class="space-y-2">
              <div class="flex items-center space-x-2">
                <i class="fas fa-map-marker-alt text-red-500"></i>
                <span><?= htmlspecialchars($venue['address']) ?></span>
              </div>
              <div class="flex items-center space-x-2">
                <i class="fas fa-phone text-green-500"></i>
                <a href="tel:<?= htmlspecialchars($venue['phone']) ?>" 
                   class="text-purple-700 hover:text-purple-800">
                  <?= htmlspecialchars($venue['phone']) ?>
                </a>
              </div>
              <div class="flex items-center space-x-2">
                <i class="fas fa-clock text-blue-500"></i>
                <span><?= htmlspecialchars($venue['hours']) ?></span>
              </div>
            </div>
            
            <div class="space-y-2">
              <div class="flex items-center space-x-2">
                <i class="fas fa-film text-purple-500"></i>
                <span><?= $venue['screens'] ?> screens</span>
              </div>
              <div class="flex items-center space-x-2">
                <i class="fas fa-users text-indigo-500"></i>
                <span><?= number_format($venue['capacity']) ?> capacity</span>
              </div>
              <div class="flex items-center space-x-2">
                <i class="fas fa-parking text-gray-500"></i>
                <span><?= htmlspecialchars($venue['parking']) ?></span>
              </div>
            </div>
          </div>
          
          <!-- Amenities -->
          <div>
            <h4 class="font-semibold mb-2">Amenities</h4>
            <div class="flex flex-wrap gap-2">
              <?php foreach ($venue['amenities'] as $amenity): ?>
                <span class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-sm">
                  <?= getAmenityIcon($amenity) ?>
                  <span><?= htmlspecialchars($amenity) ?></span>
                </span>
              <?php endforeach; ?>
            </div>
          </div>
          
          <!-- Current Movies -->
          <div>
            <h4 class="font-semibold mb-2">Now Showing</h4>
            <div class="flex flex-wrap gap-2">
              <?php 
              $movies = $currentMovies[$venue['name']] ?? [];
              foreach ($movies as $movie): 
              ?>
                <span class="px-3 py-1 rounded-md bg-gray-100 text-gray-700 text-sm">
                  <?= htmlspecialchars($movie) ?>
                </span>
              <?php endforeach; ?>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="flex gap-3 pt-4 border-t">
            <a href="/Cinema/frontend/pages/movies.php?venue=<?= urlencode($venue['name']) ?>" 
               class="flex-1 text-center bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-md">
              View Movies
            </a>
            <a href="/Cinema/frontend/pages/booking.php?venue=<?= urlencode($venue['name']) ?>" 
               class="flex-1 text-center border border-purple-700 text-purple-700 hover:bg-purple-50 px-4 py-2 rounded-md">
              Book Now
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </section>

  <!-- Venue Comparison -->
  <section class="rounded-xl border bg-white shadow-sm p-8">
    <h2 class="text-2xl font-extrabold mb-6">Compare Venues</h2>
    
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left px-4 py-3 font-semibold">Venue</th>
            <th class="text-left px-4 py-3 font-semibold">Screens</th>
            <th class="text-left px-4 py-3 font-semibold">Capacity</th>
            <th class="text-left px-4 py-3 font-semibold">Key Features</th>
            <th class="text-left px-4 py-3 font-semibold">Parking</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($venues as $venue): ?>
            <tr class="border-t">
              <td class="px-4 py-3">
                <div class="font-semibold"><?= htmlspecialchars($venue['name']) ?></div>
                <div class="text-gray-600 text-xs"><?= htmlspecialchars($venue['address']) ?></div>
              </td>
              <td class="px-4 py-3"><?= $venue['screens'] ?></td>
              <td class="px-4 py-3"><?= number_format($venue['capacity']) ?></td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-1">
                  <?php foreach (array_slice($venue['features'], 0, 2) as $feature): ?>
                    <span class="px-2 py-1 rounded bg-blue-100 text-blue-700 text-xs">
                      <?= htmlspecialchars($feature) ?>
                    </span>
                  <?php endforeach; ?>
                </div>
              </td>
              <td class="px-4 py-3 text-xs"><?= htmlspecialchars($venue['parking']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- Special Offers -->
  <section class="rounded-xl border bg-gradient-to-r from-purple-100 to-blue-100 p-8">
    <h2 class="text-2xl font-extrabold mb-6">Special Venue Offers</h2>
    
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
      <div class="bg-white rounded-lg p-4 shadow-sm">
        <h3 class="font-semibold mb-2">Downtown Cinema</h3>
        <p class="text-sm text-gray-600 mb-3">Premium experience with luxury amenities</p>
        <div class="text-purple-700 font-semibold">20% off IMAX shows</div>
      </div>
      
      <div class="bg-white rounded-lg p-4 shadow-sm">
        <h3 class="font-semibold mb-2">Grand Plex</h3>
        <p class="text-sm text-gray-600 mb-3">Modern multiplex with latest technology</p>
        <div class="text-purple-700 font-semibold">Free upgrade to premium seats</div>
      </div>
      
      <div class="bg-white rounded-lg p-4 shadow-sm">
        <h3 class="font-semibold mb-2">Sunset Drive-In</h3>
        <p class="text-sm text-gray-600 mb-3">Nostalgic drive-in experience</p>
        <div class="text-purple-700 font-semibold">Family package deals</div>
      </div>
      
      <div class="bg-white rounded-lg p-4 shadow-sm">
        <h3 class="font-semibold mb-2">Majestic Theaters</h3>
        <p class="text-sm text-gray-600 mb-3">Historic venue with cultural events</p>
        <div class="text-purple-700 font-semibold">Wine & movie combos</div>
      </div>
    </div>
  </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Venue filtering
  const filterButtons = document.querySelectorAll('.venue-filter');
  const venueCards = document.querySelectorAll('.venue-card');
  
  filterButtons.forEach(button => {
    button.addEventListener('click', function() {
      const filter = this.getAttribute('data-filter');
      
      // Update active button
      filterButtons.forEach(btn => btn.classList.remove('active', 'bg-purple-700', 'text-white'));
      filterButtons.forEach(btn => btn.classList.add('hover:bg-gray-50'));
      this.classList.add('active', 'bg-purple-700', 'text-white');
      this.classList.remove('hover:bg-gray-50');
      
      // Filter venue cards
      venueCards.forEach(card => {
        if (filter === 'all') {
          card.style.display = 'block';
        } else {
          const venueType = card.getAttribute('data-venue');
          if (venueType.includes(filter)) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        }
      });
    });
  });
});
</script>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
