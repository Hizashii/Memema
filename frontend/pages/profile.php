<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<?php
// Need to replace later with SQL
$avatar      = "../assets/img/avatar-placeholder.jpg";
$user        = [
  'full_name' => '',
  'email'     => '',
  'phone'     => '',
  'address1'  => '',
  'city'      => '',
  'state'     => '',
  'zip'       => '',
];
$states      = [];

$profileFields = [
  ['label'=>'Full Name',      'name'=>'full_name', 'type'=>'text',    'col'=>1],
  ['label'=>'Email Address',  'name'=>'email',     'type'=>'email',   'col'=>1, 'disabled'=>true, 'after'=>'<a href="#" class="text-xs text-purple-700 hover:text-purple-800">Change login email</a>'],
  ['label'=>'Phone Number',   'name'=>'phone',     'type'=>'tel',     'col'=>1],
  ['label'=>'Street Address', 'name'=>'address1',  'type'=>'text',    'col'=>2],
  ['label'=>'City',           'name'=>'city',      'type'=>'text',    'col'=>1],
  // state rendered as a select separately (for demo)
  ['label'=>'Zip Code',       'name'=>'zip',       'type'=>'text',    'col'=>2, 'wrapClass'=>'md:max-w-xs'],
];

$history = [];

$tabs = [
  ['id'=>'details','title'=>'Profile Details','active'=>true],
  ['id'=>'account','title'=>'Account Settings'],
  ['id'=>'history','title'=>'Booking History'],
];
?>

<main class="max-w-5xl mx-auto px-4 py-10 space-y-6">
  <h1 class="text-3xl font-extrabold">User Profile</h1>

  <div class="rounded-xl border bg-white shadow-sm">
    <div class="flex gap-2 border-b px-3 pt-3">
      <?php foreach ($tabs as $t): ?>
        <button
          data-tab="<?= $t['id'] ?>"
          class="tab-btn px-4 py-2 text-sm rounded-t-md <?= !empty($t['active']) ? 'bg-purple-100 text-purple-700' : 'hover:bg-gray-50' ?>">
          <?= htmlspecialchars($t['title']) ?>
        </button>
      <?php endforeach; ?>
    </div>

    <section id="tab-details" class="p-6 space-y-6 <?= $tabs[0]['active'] ? '' : 'hidden' ?>">
      <header>
        <h2 class="text-lg font-semibold">Personal Information</h2>
        <p class="text-sm text-gray-600">Manage your personal details and contact information.</p>
      </header>

      <div class="flex items-center gap-6">
        <div class="relative">
          <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="h-24 w-24 rounded-full object-cover border">
          <label class="absolute bottom-0 right-0 inline-flex items-center justify-center h-7 w-7 rounded-full bg-purple-700 text-white cursor-pointer shadow">
            <input type="file" class="hidden" accept="image/*">
            ✚
          </label>
        </div>
        <button type="button" class="rounded-md border px-3 py-2 text-sm hover:bg-gray-50">Change Avatar</button>
      </div>

      <form action="#" method="post" class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
          <?php foreach ($profileFields as $f): ?>
            <div class="<?= ($f['col'] ?? 1) === 2 ? 'md:col-span-2' : '' ?> <?= $f['wrapClass'] ?? '' ?>">
              <label class="block text-sm font-medium mb-1"><?= htmlspecialchars($f['label']) ?></label>
              <input
                type="<?= htmlspecialchars($f['type']) ?>"
                name="<?= htmlspecialchars($f['name']) ?>"
                value="<?= htmlspecialchars($user[$f['name']] ?? '') ?>"
                <?= !empty($f['disabled']) ? 'disabled' : '' ?>
                class="w-full rounded-md border px-3 py-2" />
              <?php if (!empty($f['after'])): ?>
                <div><?= $f['after'] ?></div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>

          <div>
            <label class="block text-sm font-medium mb-1">State</label>
            <select name="state" class="w-full rounded-md border px-3 py-2">
              <?php foreach ($states as $st): ?>
                <option <?= $st === ($user['state'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($st) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="flex gap-3">
          <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-md">Save Changes</button>
          <button type="button" class="rounded-md border px-4 py-2 hover:bg-gray-50">Cancel</button>
        </div>
      </form>
    </section>

    <section id="tab-account" class="hidden p-6 space-y-6">
      <header>
        <h2 class="text-lg font-semibold">Account Settings</h2>
        <p class="text-sm text-gray-600">Update password and security options.</p>
      </header>

      <?php
      $passwordFields = [
        ['label'=>'Current Password','name'=>'current_password'],
        ['label'=>'New Password','name'=>'new_password'],
        ['label'=>'Confirm New Password','name'=>'confirm_password'],
      ];
      ?>
      <form action="#" method="post" class="space-y-4 md:max-w-lg">
        <div>
          <label class="block text-sm font-medium mb-1"><?= htmlspecialchars($passwordFields[0]['label']) ?></label>
          <input type="password" name="<?= htmlspecialchars($passwordFields[0]['name']) ?>" class="w-full rounded-md border px-3 py-2" />
        </div>
        <div class="grid gap-4 md:grid-cols-2">
          <?php foreach ([$passwordFields[1], $passwordFields[2]] as $fld): ?>
            <div>
              <label class="block text-sm font-medium mb-1"><?= htmlspecialchars($fld['label']) ?></label>
              <input type="password" name="<?= htmlspecialchars($fld['name']) ?>" class="w-full rounded-md border px-3 py-2" />
            </div>
          <?php endforeach; ?>
        </div>

        <div class="flex gap-3">
          <button class="bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-md">Update Password</button>
          <button type="button" class="rounded-md border px-4 py-2 hover:bg-gray-50">Cancel</button>
        </div>
      </form>

      <div class="pt-6 border-t">
        <h3 class="text-sm font-semibold mb-2">Two-Factor Authentication</h3>
        <p class="text-sm text-gray-600 mb-3">Add an extra layer of security to your account.</p>
        <button class="rounded-md border px-4 py-2 hover:bg-gray-50">Set up 2FA</button>
      </div>
    </section>

    <section id="tab-history" class="hidden p-6 space-y-4">
      <header>
        <h2 class="text-lg font-semibold">Booking History</h2>
        <p class="text-sm text-gray-600">Your recent tickets and reservations.</p>
      </header>

      <div class="overflow-x-auto rounded-xl border bg-white">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left px-4 py-3">Movie</th>
              <th class="text-left px-4 py-3">Date</th>
              <th class="text-left px-4 py-3">Venue</th>
              <th class="text-left px-4 py-3">Seats</th>
              <th class="text-left px-4 py-3">Total</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($history as $row): ?>
              <tr class="border-t">
                <td class="px-4 py-3"><?= htmlspecialchars($row['movie']) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($row['datetime']) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($row['venue']) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($row['seats']) ?></td>
                <td class="px-4 py-3">$<?= number_format($row['total'], 2) ?></td>
                <td class="px-4 py-3">
                  <a href="#" class="text-purple-700 hover:text-purple-800">View</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</main>

<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const tab = btn.dataset.tab;
    document.querySelectorAll('.tab-btn').forEach(b => {
      b.classList.remove('bg-purple-100','text-purple-700');
      b.classList.add('hover:bg-gray-50');
    });
    btn.classList.add('bg-purple-100','text-purple-700');
    btn.classList.remove('hover:bg-gray-50');

    ['details','account','history'].forEach(id => {
      document.getElementById('tab-' + id).classList.toggle('hidden', id !== tab);
    });
  });
});
</script>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
