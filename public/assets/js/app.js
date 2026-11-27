(() => {
  const menu = document.querySelector('#menuToggle');
  const nav = document.querySelector('#sidebar');
  const shade = document.querySelector('#sidebarShade');
  if (menu && nav && shade) {
    menu.addEventListener('click', () => { nav.classList.toggle('open'); shade.classList.toggle('open'); });
    shade.addEventListener('click', () => { nav.classList.remove('open'); shade.classList.remove('open'); });
  }
  document.querySelectorAll('.alert-dismiss').forEach(btn => btn.addEventListener('click', () => btn.closest('.alert')?.remove()));
  document.querySelectorAll('[data-preview-for]').forEach(img => {
    const file = document.querySelector('#' + img.dataset.previewFor);
    if (!file) return;
    file.addEventListener('change', () => {
      if (!file.files || !file.files.length) return;
      const u = URL.createObjectURL(file.files[0]);
      img.src = u;
      img.onload = () => URL.revokeObjectURL(u);
    });
  });
  const product = document.querySelector('#sale-product');
  const qty = document.querySelector('#sale-quantity');
  if (product && qty) {
    const money = n => '₱' + Number(n).toLocaleString('en-PH', {minimumFractionDigits:2,maximumFractionDigits:2});
    const render = () => {
      const option = product.selectedOptions[0];
      const price = option?.dataset.price ? Number(option.dataset.price) : 0;
      const stock = option?.dataset.stock ? Number(option.dataset.stock) : 0;
      const count = Math.max(0, Number(qty.value) || 0);
      document.querySelector('#preview-price').textContent = money(price);
      document.querySelector('#preview-quantity').textContent = count;
      document.querySelector('#preview-total').textContent = money(price * count);
      const notice = document.querySelector('#stock-notice');
      if (!price) notice.textContent = 'Select a product to check available stock.';
      else if (count > stock) { notice.textContent = `Only ${stock} unit(s) available — reduce the quantity.`; notice.classList.add('is-error'); }
      else { notice.textContent = `${stock} unit(s) currently available.`; notice.classList.remove('is-error'); }
    };
    product.addEventListener('change', render);
    qty.addEventListener('input', render);
    render();
  }
})();
