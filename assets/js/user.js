import axios from "axios";

const amisLinks = document.querySelectorAll(".amis");
const suivisLiniks = document.querySelectorAll(".suivis");
const image_profil = document.getElementById("image_profil");
const image_profil1 = document.getElementById("image_profil1");
const nom_profil = document.getElementById("nom_profil");
const modifie_profil = document.getElementById("modifie_profil");

// const navLinks = document.querySelectorAll('.public-profil-nav a');
// const pageDivs = document.querySelectorAll('.public-profil-na .lespageprofil');

const page_profil = document.getElementById("page-profil");
const page_amis = document.getElementById("page-amis");
const page_suivis = document.getElementById("page-suivis");

/**
 * Le profil d'ami d'un utilisateur***
 */
Array.from(amisLinks, (ami) => {
  ami.addEventListener("click", (e) => {
    e.preventDefault();
    let url = ami.attributes["href"].value;
    // Ajax avec Axios
    axios.get(url).then((response) => {
      if (response.data == 502) {
        //Redirection vers la page de connexion si non connécter
        return (window.location = "/connexion");
      }
      let id = response.data.userProfil[0].id
      let image = response.data.userProfil[0].image
      let nom = response.data.userProfil[0].nom
      let slug = response.data.userProfil[0].slug
      let amis= response.data.amis
      let suivis= response.data.followUsers
      console.log(amis);

      // page_profil.innerText = 'Profil '+nom
      // modifie_profil.attributes["href"].value = '/profil'
      // image_profil.attributes["src"].value = image
      // image_profil.attributes["alt"].value = 'image de '+nom
      // image_profil1.attributes["src"].value = image
      // image_profil1.attributes["alt"].value = 'image de '+nom
      // nom_profil.innerHTML = nom
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
    // Ajax avec Axios
    axios.get(url).then((response) => {
      if (response.data == 502) {
        //Redirection vers la page de connexion si non connécter
        return (window.location = "/connexion");
      }
      let id = response.data.userProfil[0].id
      let image = response.data.userProfil[0].image
      let nom = response.data.userProfil[0].nom
      let slug = response.data.userProfil[0].slug
      let amis= response.data.amis
      let suivis= response.data.followUsers
      console.log(suivis);
      // modifie_profil.innerText = 'Mon profil'
      // modifie_profil.attributes["href"].value = '/profil'
      // image_profil.attributes["src"].value = image
      // image_profil.attributes["alt"].value = 'image de '+nom
      // image_profil1.attributes["src"].value = image
      // image_profil1.attributes["alt"].value = 'image de '+nom
      // nom_profil.innerHTML = nom
    });
  });
});