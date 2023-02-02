import axios from "axios";

const btn_close = document.querySelector('.btn-close')
if(btn_close) btn_close.addEventListener('click', () => btn_close.parentNode.remove());

let thumbs = document.querySelectorAll('.fa-thumbs-up')
// console.log(thumbs);
Array.from(thumbs, thumb => {
    thumb.addEventListener('click', (e) =>{
    let postId = thumb.attributes["data-id"].value
        console.log(postId);
    })
})
// axios.get('publication/like').then(response => {
//     for(let result of response.data.data) console.log(result);
// })
// #00b5ff