const navLinks = document.querySelectorAll('.public-profil-nav a');
const pageDivs = document.querySelectorAll('.public-profil-na .lespageprofil');

navLinks.forEach(link => {
    link.addEventListener('click', event => {
        event.preventDefault();

        // Masque toutes les pages et retire la classe active
        pageDivs.forEach(pageDiv => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
          });
            pageDiv.classList.remove('active');
        });

        // Affiche la page correspondant au lien cliqué
        const targetPageId = link.getAttribute('href');
        const targetPage = document.querySelector(targetPageId);

        if (targetPage) {
            targetPage.classList.add('active');
        }
    });
});
