/**
 * ferme le message compte non active.
 */
const btn_close = document.querySelector(".btn-close");
if (btn_close)
  btn_close.addEventListener("click", () => btn_close.parentNode.remove());

  /**
   * supprimer le contenu des messages flash aprés 5 secondes
   */
  const flash_messages = document.getElementById('flash-messages')
  setTimeout(() => {
    flash_messages.innerHTML = "";
  }, 5000);
  


