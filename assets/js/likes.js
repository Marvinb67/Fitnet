import axios from "axios";
import { setTimeout } from "core-js";

/**
 * Gestion du systéme du like/dislike par ajax
 */
// selection des icons like/dislike
let thumbs = document.querySelectorAll(".reactions");

// Array.from(thumbs, (thumb) => {
  // Fournis les stats des likes/dislikes
  axios.get("reaction/stats").then((response) => {
    let data = response.data.likes
    for(let stat of data){
    let idPublication = stat.idPublication;
    let countLikes = stat.countLikes;
    let countDisLikes = stat.countDisLikes;
    const likesSpan = document.getElementById(`j-aime-${idPublication}`);
    const dislikesSpan = document.getElementById(`j-aimePas-${idPublication}`);
    likesSpan.innerHTML = countLikes;
    dislikesSpan.innerHTML= countDisLikes;
    }

  });
// });
// On boucle sur toutes les icons
Array.from(thumbs, (thumb) => {
  // Ecouteur d'evenement sur l'icon cloquer
  thumb.addEventListener("click", () => {
    // l'icon cliquer
    let aimer = thumb.classList.contains("fa-thumbs-up") ? true : false;
    // Class à ajouter à l'icon
    let class1 = aimer ? "j-aime" : "j-aimePas";
    // Récupération de l'Id de la publication
    let postId = thumb.attributes["data-id"].value;
    // Compte en temps reel des likes/DisLikes
    const countSpanLikes = document.getElementById(`j-aime-${postId}`);
    const countSpanDisLikes = document.getElementById(`j-aimePas-${postId}`);
    // Génére l'Url like/dislike
    const url = `reaction/like/${postId}-${aimer}`;
    // Ajax avec Axios
    axios.get(url).then((response) => {
      if (response.data == 502) {
        //Redirection vers la page de connexion si non connécter
        return (window.location = "/connexion");
      }
      // Récupération des données envoyer par le controller aprés le traitement
      let message = response.data.message;
      let reaction = response.data.reaction;
      let countLikes = response.data.countLikes;
      let countDisLikes = response.data.countDisLikes;
      // Au cas d'une réaction on rafraichi les donnees afficher
      if (reaction == 1) {
        thumb.classList.toggle(class1);
        countSpanLikes.innerHTML = countLikes;
        countSpanDisLikes.innerHTML = countDisLikes;
      }
      // Au cas d'annulation du like/dislike on rafraichi les donnees afficher
      else {
        class1 === "j-aimePas"
          ? popup(thumb, message, 80)
          : popup(thumb, message, 112);
        thumb.classList.remove(class1);
        countSpanLikes.innerHTML = countLikes;
        countSpanDisLikes.innerHTML = countDisLikes;
      }
    });
  });
});
// Fonction qui gére le petit popup au cas d'annulation du like/dislike
const popup = (element, message, left) => {
  let pop = element.parentElement.parentElement.children[0].children[0];
  pop.innerHTML = message;
  pop.style.setProperty("--popAfterLeft", left + "px");
  pop.classList.toggle("show");
  setTimeout(() => {
    pop.innerHTML = "";
    pop.classList.remove("show");
  }, 3000);
};
// #00b5ff
