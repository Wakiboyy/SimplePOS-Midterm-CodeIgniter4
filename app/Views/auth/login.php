<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Sign in · SimplePOS</title><link rel="icon" href="<?= base_url('assets/img/favicon.svg') ?>" type="image/svg+xml"><link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>"></head>
<body class="login-body"><main class="login-shell">
  <section class="login-story"><div class="login-brand"><span class="brand-mark">S</span><span>simplepos.</span></div><div class="login-story-inner"><span class="login-kicker">A SMARTER WAY TO SELL</span><h1>Every sale.<br><em>Under control.</em></h1><p>Manage inventory, build customer relationships, and track every transaction — from one clean dashboard.</p><div class="login-feature"><span>✓</span> Secure staff sign-in</div><div class="login-feature"><span>✓</span> Real-time stock tracking</div><div class="login-feature"><span>✓</span> Complete sales records</div></div><div class="login-footer">IT0049 · WEB SYSTEM TECHNOLOGIES</div></section>
  <section class="login-panel"><div class="login-form-card"><div class="login-overline">WELCOME BACK</div><h2>Sign in to your account</h2><p class="muted">Enter your staff credentials to access the workspace.</p>
    <?php $errors=session()->getFlashdata('errors') ?? []; ?>
    <?php if (session()->getFlashdata('error')): ?><div class="alert alert-error" role="alert"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?><div class="alert alert-success" role="status"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>
    <?php if ($errors): ?><div class="alert alert-error" role="alert"><?php foreach ($errors as $err): ?><div><?= esc($err) ?></div><?php endforeach; ?></div><?php endif; ?>
    <form method="post" action="<?= site_url('login') ?>" class="login-form"><?= csrf_field() ?>
      <label class="field"><span>Username</span><input name="username" value="<?= esc(old('username') ?? '', 'attr') ?>" maxlength="50" placeholder="Enter your username" required autocomplete="username"></label>
      <label class="field"><span>Password</span><input type="password" name="password" placeholder="Enter your password" required autocomplete="current-password"></label>
      <button class="btn btn-primary btn-full" type="submit">Sign in <span>→</span></button>
    </form><p class="login-note">No default password is included. First-time setup: run <code>php spark db:seed AdminSeeder</code> from PowerShell and save the generated password.</p>
  </div><div class="login-bottom">SimplePOS · Secure workspace</div></section>
</main></body></html>
