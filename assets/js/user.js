import axios from "axios";

const amisLiknks = document.querySelectorAll(".amis");
const suivisLiniks = document.querySelectorAll(".suivis");
const image_profil = document.getElementById("image_profil");
const nom_profil = document.getElementById("nom_profil");
const modifie_profil = document.getElementById("modifie_profil");

/**
 * Le profil d'ami d'un utilisateur
 */
Array.from(amisLiknks, (ami) => {
  ami.addEventListener("click", (e) => {
    e.preventDefault();
    let url = ami.attributes["href"].value;
    // Ajax avec Axios
    axios.get(url).then((response) => {
      if (response.data == 502) {
        //Redirection vers la page de connexion si non connécter
        return (window.location = "/connexion");
      }
      let image = response.data.userProfil[0].image
      let nom = response.data.userProfil[0].nom
      modifie_profil.innerText = 'Mon profil'
      modifie_profil.attributes["href"].value = '/profil'
      image_profil.attributes["src"].value = image
      image_profil.attributes["alt"].value = 'image de '+nom
      nom_profil.innerHTML = nom
    });
  });
});

/**
 * Le profil de suivi d'un utilisateur
 */
Array.from(suivisLiniks, (suivi) => {
  suivi.addEventListener("click", (e) => {
    e.preventDefault();
    let url = suivi.attributes["href"].value;
    console.log(url);
    // Ajax avec Axios
    axios.get(url).then((response) => {
      if (response.data == 502) {
        //Redirection vers la page de connexion si non connécter
        return (window.location = "/connexion");
      }
      let image = response.data.userProfil[0].image
      let nom = response.data.userProfil[0].nom
      modifie_profil.innerText = 'Mon profil'
      modifie_profil.attributes["href"].value = '/profil'
      image_profil.attributes["src"].value = image
      image_profil.attributes["alt"].value = 'image de '+nom
      nom_profil.innerHTML = nom
    });
  });
});