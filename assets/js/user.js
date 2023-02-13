import axios from "axios";

const amisLiknks = document.querySelectorAll(".amis");
const suivisLiniks = document.querySelectorAll(".suivis");
const image_profil = document.getElementById("image_profil");
const nom_profil = document.getElementById("nom_profil");
const modifie_profil = document.getElementById("modifie_profil");
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
      console.log(response.data.userProfil[0]);
      modifie_profil.innerText = 'Mon profil'
      modifie_profil.attributes["href"].value = '/profil'
      image_profil.attributes["src"].value = image
      image_profil.attributes["alt"].value = 'image de '+nom
      nom_profil.innerHTML = nom
    });
  });
});
