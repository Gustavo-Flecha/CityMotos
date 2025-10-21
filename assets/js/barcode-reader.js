// JavaScript para redirigir búsquedas a resultados de productos 
document.addEventListener('DOMContentLoaded', function() {
  const searchForm = document.querySelector('form[role="search"]');
  const searchInput = searchForm?.querySelector('input[type="search"]');

  if (!searchForm || !searchInput) return;

  searchForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const query = searchInput.value.trim();

    if (!query) return;

    // Redirige directamente a resultados de WooCommerce
    window.location.href = `${window.location.origin}/?post_type=product&s=${encodeURIComponent(query)}`;
  });
});
