import axios from "axios";

const btn_close = document.querySelector('.btn-close')
if(btn_close) btn_close.addEventListener('click', () => btn_close.parentNode.remove());

let thumbs = document.querySelectorAll('.fa-thumbs-up')
axios.get('publication/like').then(response => {
    console.log(response.data)
})