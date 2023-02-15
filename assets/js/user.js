import axios from "axios";

// const container = document.getElementById('contain');
// const div = document.createElement('div');
// div.innerHTML = `<div class="col my-2">
// <h4>Les Amis</h4>
// {% for ami in amis %}
//   <div>
//     <a id="ami-{{ami.id}}" class="link-secondary text-decoration-none amis" href="{{ path('app_user_detail', { slug: ami.slug }) }}"><img class="img-profil" src="{{ ami.image }}" alt="{{ ami.nom }}" width="50px" height="50px"/>
//       {{ ami.nom }}
//       {{ ami.prenom }}</a><br/>
//   {% endfor %}
// </div>
// </div>
// `
// document.getElementById('app_amie').addEventListener('click', (e)=>{ 
//   e.preventDefault();
//   container.appendChild(div)
// });

const amisLiknks = document.querySelectorAll(".amis");
const suivisLiniks = document.querySelectorAll(".suivis");
const image_profil = document.getElementById("image_profil");
const image_profil1 = document.getElementById("image_profil1");
const nom_profil = document.getElementById("nom_profil");
const modifie_profil = document.getElementById("modifie_profil");

/**
 * Le profil d'ami d'un utilisateur***
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
      image_profil1.attributes["src"].value = image
      image_profil1.attributes["alt"].value = 'image de '+nom
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
      image_profil1.attributes["src"].value = image
      image_profil1.attributes["alt"].value = 'image de '+nom
      nom_profil.innerHTML = nom
    });
  });
});