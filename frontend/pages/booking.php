<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<?php
  // ---- Server data you could fetch from DB ----
  $movieTitle   = $_GET['movie']  ?? 'Oppenheimer';
  $showDate     = $_GET['date']   ?? date('Y-m-d');
  $pricePerSeat = 12.50;

  $rows = str_split('ABCDEFGHIJ');  
  $cols = 12;                 
?>

<main class="max-w-4xl mx-auto px-4 py-10 space-y-8">

  <!-- Select showing -->
  <section class="rounded-xl border bg-white shadow-sm p-5 space-y-4">
    <h2 class="text-2xl font-extrabold">Select Your Showing</h2>

    <form id="showingForm" class="grid gap-4 md:grid-cols-2" method="get">
      <div>
        <label class="block text-sm font-medium mb-1">Movie</label>
        <select name="movie" class="w-full rounded-md border px-3 py-2">
          <option <?= $movieTitle==='Oppenheimer'?'selected':''; ?>>Oppenheimer</option>
          <option <?= $movieTitle==='The Dark Knight'?'selected':''; ?>>The Dark Knight</option>
          <option <?= $movieTitle==='Joker'?'selected':''; ?>>Joker</option>
          <option <?= $movieTitle==='The Godfather'?'selected':''; ?>>The Godfather</option>
          <option <?= $movieTitle==='Pulp Fiction'?'selected':''; ?>>Pulp Fiction</option>
          <option <?= $movieTitle==='Alien'?'selected':''; ?>>Alien</option>
          <option <?= $movieTitle==='Free Guy'?'selected':''; ?>>Free Guy</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Date</label>
        <input type="date" name="date" value="<?= htmlspecialchars($showDate) ?>"
               class="w-full rounded-md border px-3 py-2">
      </div>
      <div class="md:col-span-2">
        <button class="mt-1 bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-md">
          Update Show
        </button>
      </div>
    </form>
  </section>

  <section class="rounded-xl border bg-white shadow-sm p-5">
    <h2 class="text-2xl font-extrabold text-center">Choose Your Seats</h2>

    <!-- “Screen” bar -->
    <div class="mx-auto mt-6 w-3/4 h-2 rounded-full bg-purple-700"></div>

    <!-- Legend -->
    <div class="mt-5 flex items-center justify-center gap-6 text-sm">
      <span class="inline-flex items-center gap-2">
        <span class="h-4 w-6 rounded-md border bg-white"></span> Available
      </span>
      <span class="inline-flex items-center gap-2">
        <span class="h-4 w-6 rounded-md border bg-purple-700"></span> Selected
      </span>
      <span class="inline-flex items-center gap-2">
        <span class="h-4 w-6 rounded-md border bg-gray-300"></span> Reserved
      </span>
    </div>

    <!-- Seat grid -->
    <form id="seatForm" class="mt-8 w-fit mx-auto space-y-2" method="post" action="/Cinema/frontend/pages/checkout.php">
      <!-- Header numbers -->
      <div class="flex items-center justify-center gap-2 mb-1">
        <div class="w-6"></div>
        <div class="grid grid-cols-12 gap-2 text-xs text-gray-600">
          <?php for ($c=1; $c<=$cols; $c++): ?>
            <div class="text-center"><?= $c ?></div>
          <?php endfor; ?>
        </div>
      </div>

      <?php foreach ($rows as $r): ?>
        <div class="flex items-center justify-center gap-2">
          <div class="w-6 text-xs text-gray-600 text-right pr-1"><?= $r ?></div>
          <div class="grid grid-cols-12 gap-2">
            <?php for ($c=1; $c<=$cols; $c++):
              $id = $r.$c;
              $isReserved = isset($reserved[$id]);
            ?>
              <label class="relative">
                <input
                  type="checkbox"
                  name="seats[]"
                  value="<?= $id ?>"
                  <?= $isReserved ? 'disabled' : '' ?>
                  class="peer sr-only">
                <span class="
                  w-9 h-9 rounded-md border text-xs flex items-center justify-center
                  select-none
                  <?= $isReserved
                      ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                      : 'bg-white hover:bg-gray-50 cursor-pointer peer-checked:bg-purple-700 peer-checked:text-white' ?>
                ">
                  <?= $c ?>
                </span>
              </label>
            <?php endfor; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <input type="hidden" name="movie" value="<?= htmlspecialchars($movieTitle) ?>">
      <input type="hidden" name="date"  value="<?= htmlspecialchars($showDate)  ?>">
      <input type="hidden" name="price" value="<?= number_format($pricePerSeat,2,'.','') ?>">
    </form>
  </section>

  <section class="rounded-xl border bg-white shadow-sm p-5 space-y-4">
    <h2 class="text-2xl font-extrabold">Booking Summary</h2>

    <div>
      <div class="font-semibold text-purple-700"><?= htmlspecialchars($movieTitle) ?></div>
      <div class="text-sm text-gray-600 mt-1">Selected Seats (<span id="seatCount">0</span>):</div>
      <div id="seatList" class="text-sm italic text-gray-500 mt-1">No seats selected yet.</div>
    </div>

    <div class="text-sm text-gray-700">Price per seat: $<?= number_format($pricePerSeat,2) ?></div>

    <div class="flex items-center justify-between border-t pt-4 font-semibold">
      <span>Total:</span>
      <span id="totalPrice">$0.00</span>
    </div>

    <button form="seatForm"
            class="w-full bg-purple-700 hover:bg-purple-800 text-white px-4 py-3 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
            id="checkoutBtn" disabled>
      Proceed to Checkout
    </button>
  </section>
</main>

<script>
(function(){
  const price = <?= json_encode($pricePerSeat) ?>;
  const seatForm = document.getElementById('seatForm');
  const countEl  = document.getElementById('seatCount');
  const listEl   = document.getElementById('seatList');
  const totalEl  = document.getElementById('totalPrice');
  const btn      = document.getElementById('checkoutBtn');

  function money(n){ return '$' + n.toFixed(2); }

  function refresh(){
    const checked = [...seatForm.querySelectorAll('input[name="seats[]"]:checked')]
                    .map(i => i.value).sort();
    countEl.textContent = checked.length;
    if (checked.length === 0) {
      listEl.textContent = 'No seats selected yet.';
      listEl.classList.add('italic','text-gray-500');
      btn.disabled = true;
      totalEl.textContent = money(0);
    } else {
      listEl.textContent = checked.join(', ');
      listEl.classList.remove('italic','text-gray-500');
      btn.disabled = false;
      totalEl.textContent = money(checked.length * price);
    }
  }

  seatForm.addEventListener('change', refresh);
  refresh();
})();
</script>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
