import axios from "axios";

const btn_close = document.querySelector(".btn-close");
if (btn_close)
  btn_close.addEventListener("click", () => btn_close.parentNode.remove());

let thumbs = document.querySelectorAll(".reactions");

Array.from(thumbs, (thumb) => {
  thumb.addEventListener("click", (e) => {

    let aimer = thumb.classList.contains("fa-thumbs-up") ?  'true' : 'false';
    let class1 = '';
    aimer == 'true'? class1 = 'j-aime' : class1 = 'j-aimePas';
    let postId = thumb.attributes["data-id"].value;
    const countSpan = document.getElementById(`${class1}-${postId}`);
    const url = `reaction/like/${postId}-${aimer}`;

    axios.get(url).then((response) => {

      let reaction = response.data.reaction;
      let count = response.data.count;
      reaction == 1
        ? thumb.classList.add(class1)
        : thumb.classList.remove(class1);
      countSpan.innerHTML = count;
    console.log(thumb.nextElementSibling.innerHTML)

    });

  });

});

// #00b5ff
