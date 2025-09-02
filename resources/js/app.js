import './bootstrap';

// Masquer l'écran de chargement après le rendu
window.addEventListener('load', () => {
  const overlay = document.getElementById('loadingOverlay');
  if (overlay) {
    overlay.classList.add('hidden');
    setTimeout(() => {
      overlay.remove();
    }, 350);
  }
});
