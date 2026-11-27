<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="color-scheme" content="light">
  <meta name="description" content="SimplePOS inventory and sales management">
  <title><?= esc($title ?? 'Dashboard') ?> · SimplePOS</title>
  <link rel="icon" href="<?= base_url('assets/img/favicon.svg') ?>" type="image/svg+xml">
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
  <script src="<?= base_url('assets/js/app.js') ?>" defer></script>
</head>
<body>
<div class="app-shell">
  <div class="sidebar-shade" id="sidebarShade" aria-hidden="true"></div>
  <aside class="sidebar" id="sidebar">
    <div class="brand"><div class="brand-mark">S</div><div><strong>simplepos<span class="brand-dot">.</span></strong><small>MANAGEMENT SUITE</small></div></div>
    <div class="sidebar-caption">WORKSPACE</div>
    <?php $path = trim(uri_string(), '/'); ?>
    <nav aria-label="Main navigation" class="navigation">
      <a class="nav-link <?= ($path === '' || $path === 'dashboard') ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>"><span class="nav-icon">⌂</span><span>Overview</span></a>
      <a class="nav-link <?= str_starts_with($path,'products') ? 'active' : '' ?>" href="<?= site_url('products') ?>"><span class="nav-icon">▦</span><span>Products</span></a>
      <a class="nav-link <?= str_starts_with($path,'customers') ? 'active' : '' ?>" href="<?= site_url('customers') ?>"><span class="nav-icon">♙</span><span>Customers</span></a>
      <a class="nav-link <?= str_starts_with($path,'staff') ? 'active' : '' ?>" href="<?= site_url('staff') ?>"><span class="nav-icon">♧</span><span>Staff</span></a>
      <a class="nav-link <?= str_starts_with($path,'sales') ? 'active' : '' ?>" href="<?= site_url('sales') ?>"><span class="nav-icon">⇄</span><span>Sales history</span></a>
    </nav>
    <div class="sidebar-bottom">
      <div class="sidebar-helper"><span class="helper-star">✧</span><strong>Quick checkout</strong><p>Record a sale and update your stock instantly.</p><a href="<?= site_url('sales/new') ?>" class="helper-link">New sale <span>↗</span></a></div>
      <div class="sidebar-profile"><span class="sidebar-avatar"><?= esc(strtoupper(substr((string)session()->get('staff_name'),0,1))) ?></span><div class="sidebar-user"><strong><?= esc(session()->get('staff_name') ?? 'Staff') ?></strong><small>@<?= esc(session()->get('staff_username') ?? 'user') ?></small></div><form method="post" action="<?= site_url('logout') ?>" title="Log out" class="signout-form"><?= csrf_field() ?><button aria-label="Sign out" type="submit">↪</button></form></div>
    </div>
  </aside>
  <div class="main-area">
    <header class="topbar"><div class="topbar-left"><button type="button" id="menuToggle" class="menu-toggle" aria-label="Toggle menu">☰</button><span class="crumb">Workspace</span><span class="crumb-sep">/</span><strong><?= esc($title ?? 'Overview') ?></strong></div><div class="topbar-right"><span class="today-label"><?= esc(date('D, d M Y')) ?></span><span class="status-dot"></span><span class="status-word">System active</span></div></header>
    <main class="page-content">
      <?php $errors = session()->getFlashdata('errors') ?? []; ?>
      <?php if (session()->getFlashdata('success')): ?><div class="alert alert-success" role="status"><strong>✓ Success</strong><span><?= esc(session()->getFlashdata('success')) ?></span><button type="button" class="alert-dismiss" aria-label="Dismiss notification">×</button></div><?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?><div class="alert alert-error" role="alert"><strong>! Action required</strong><span><?= esc(session()->getFlashdata('error')) ?></span><button type="button" class="alert-dismiss" aria-label="Dismiss notification">×</button></div><?php endif; ?>
      <?php if (!empty($errors)): ?><div class="alert alert-error" role="alert"><strong>! Check your entries</strong><ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul><button type="button" class="alert-dismiss" aria-label="Dismiss notification">×</button></div><?php endif; ?>
      <?= $this->renderSection('content') ?>
    </main>
    <footer class="footer">© <?= date('Y') ?> SimplePOS · IT0049 Midterm Project <span>Built with CodeIgniter 4</span></footer>
  </div>
</div>
</body>
</html>
