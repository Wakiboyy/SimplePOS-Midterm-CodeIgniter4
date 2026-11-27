<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="hero-banner"><div><span class="eyebrow"><span class="eyebrow-dot"></span> SALES OVERVIEW</span><h1>Good to see you, <?= esc(explode(' ', trim((string)(session()->get('staff_name') ?: 'there')))[0]) ?><span class="period">.</span></h1><p>Here's what's happening across your store today. Everything you need, all in one place.</p></div><a href="<?= site_url('sales/new') ?>" class="btn btn-white">＋ Record new sale</a><div class="hero-graphic" aria-hidden="true"><div class="hero-circle circle-one"></div><div class="hero-circle circle-two"></div></div></section>
<div class="section-heading"><div><span class="eyebrow dark">PERFORMANCE</span><h2>Business at a glance</h2></div><span class="muted small">All-time totals</span></div>
<section class="metric-grid" aria-label="Key performance figures">
  <div class="metric-card"><div class="metric-top"><span>Total revenue</span><span class="metric-icon icon-purple">₱</span></div><strong>₱<?= number_format((float)$revenue, 2) ?></strong><small>From completed transactions</small></div>
  <div class="metric-card"><div class="metric-top"><span>Completed sales</span><span class="metric-icon icon-blue">⇄</span></div><strong><?= number_format((int)$sales) ?></strong><small>Transactions on record</small></div>
  <div class="metric-card"><div class="metric-top"><span>Active products</span><span class="metric-icon icon-orange">▦</span></div><strong><?= number_format((int)$products) ?></strong><small>Available in catalog</small></div>
  <div class="metric-card"><div class="metric-top"><span>Customers</span><span class="metric-icon icon-green">♙</span></div><strong><?= number_format((int)$customers) ?></strong><small><?= number_format((int)$staff) ?> active staff accounts</small></div>
</section>
<div class="dashboard-two-col">
 <section class="panel"><div class="panel-heading"><div><span class="eyebrow dark">LATEST ACTIVITY</span><h3>Recent transactions</h3></div><a class="text-link" href="<?= site_url('sales') ?>">View all →</a></div>
 <div class="table-wrap"><table class="data-table"><thead><tr><th>Transaction</th><th>Product</th><th>Qty</th><th class="align-right">Amount</th></tr></thead><tbody><?php if (!$recent): ?><tr><td colspan="4"><div class="empty compact"><strong>No sales yet</strong><span>Your first sale will appear here.</span></div></td></tr><?php else: foreach($recent as $sale): ?><tr><td><a href="<?= site_url('sales/' . $sale['id']) ?>" class="row-link">#<?= str_pad((string)$sale['id'],4,'0',STR_PAD_LEFT) ?></a><div class="subtext"><?= esc(date('M j, g:i A', strtotime($sale['created_at']))) ?></div></td><td><?= esc($sale['product_name']) ?></td><td><?= (int)$sale['quantity'] ?></td><td class="align-right strong">₱<?= number_format((float)$sale['total_price'],2) ?></td></tr><?php endforeach; endif; ?></tbody></table></div>
 </section>
 <section class="panel"><div class="panel-heading"><div><span class="eyebrow dark">INVENTORY WATCH</span><h3>Low stock alerts</h3></div><a class="text-link" href="<?= site_url('products') ?>">Products →</a></div>
 <div class="stock-list"><?php if (!$lowStock): ?><div class="empty"><div class="empty-illustration">✓</div><strong>Stock looks healthy</strong><span>No products have five units or fewer.</span></div><?php else: foreach($lowStock as $product): ?><div class="stock-item"><span class="stock-item-icon">!</span><div><strong><?= esc($product['name']) ?></strong><small><?= (int)$product['stock_quantity'] ?> unit(s) remaining</small></div><span class="pill pill-warn">Low</span></div><?php endforeach; endif; ?></div>
 </section>
</div>
<?= $this->endSection() ?>
