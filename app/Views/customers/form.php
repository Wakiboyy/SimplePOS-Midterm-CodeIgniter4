<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $editing = $customer !== null; $action = $editing ? site_url('customers/'.$customer['id']) : site_url('customers'); ?>
<div class="backline"><a href="<?= site_url('customers') ?>">← Back to customers</a></div><section class="page-heading"><div><span class="eyebrow dark">CUSTOMER MANAGEMENT</span><h1><?= $editing ? 'Edit customer' : 'Add new customer' ?></h1><p>Contact details help you connect sales to a customer.</p></div></section>
<form action="<?= $action ?>" method="post" class="form-page"><?= csrf_field() ?><section class="panel form-panel"><div class="panel-heading"><h3>Customer information</h3><span class="muted small">Fields marked * are required</span></div><div class="form-padding"><div class="form-grid">
<label class="field field-span"><span>Full name *</span><input type="text" name="full_name" maxlength="100" required placeholder="e.g. Maria Santos" value="<?= esc(old('full_name',$customer['full_name'] ?? ''),'attr') ?>"></label>
<label class="field"><span>Email address *</span><input type="email" name="email" maxlength="100" required placeholder="name@example.com" value="<?= esc(old('email',$customer['email'] ?? ''),'attr') ?>"></label>
<label class="field"><span>Phone number (optional)</span><input type="tel" name="phone" maxlength="20" placeholder="09XXXXXXXXX" value="<?= esc(old('phone',$customer['phone'] ?? ''),'attr') ?>"></label>
</div></div></section><div class="form-actions"><a class="btn btn-secondary" href="<?= site_url('customers') ?>">Cancel</a><button class="btn btn-primary" type="submit"><?= $editing ? 'Save changes' : 'Create customer' ?> →</button></div></form>
<?= $this->endSection() ?>
