(self.webpackChunk=self.webpackChunk||[]).push([[106],{5134:(e,i,t)=>{"use strict";t(91038),t(78783),t(92222);var n=t(12956),o=t(81486),a=document.querySelectorAll(".reactions");Array.from(a,(function(e){e.addEventListener("click",(function(){var i=!!e.classList.contains("fa-thumbs-up"),t=i?"j-aime":"j-aimePas",o=e.attributes["data-id"].value,a=document.getElementById("j-aime-".concat(o)),c=document.getElementById("j-aimePas-".concat(o)),r="".concat(window.location.origin,"/reaction/like/").concat(o,"-").concat(i);n.Z.get(r).then((function(i){if(502==i.data)return window.location="/connexion";var n=i.data.message,o=i.data.reaction,r=i.data.countLikes,s=i.data.countDisLikes,l=i.data.aimeOuPas;switch(l){case!0:e.style.color="#0a58ca";break;case!1:e.style.color="red";break;case null:e.style.color="#212529"}switch(1==o?(e.classList.toggle(t),a.innerHTML=r,c.innerHTML=s):(d(e,n,"j-aimePas"===t?112:80),e.classList.remove(t),a.innerHTML=r,c.innerHTML=s),l){case!0:a.previousSibling.previousSibling.style.color="#0a58ca",c.previousSibling.previousSibling.style.color="#212529";break;case!1:c.previousSibling.previousSibling.style.color="red",a.previousSibling.previousSibling.style.color="#212529";break;case null:a.previousSibling.previousSibling.style.color="#212529",c.previousSibling.previousSibling.style.color="#212529"}})).catch((function(e){}))}))}));var c=document.querySelector(".reactions"),r=document.querySelectorAll(".fa-thumbs-down");if(c){var s=c.attributes["data-id"].value,l=["/search-form","/publication"],u=window.location.pathname===l[1]||window.location.pathname===l[0]||"/"===window.location.pathname?"".concat(window.location.origin,"/reaction/stats"):"".concat(window.location.origin,"/reaction/stats/").concat(s);n.Z.get(u).then((function(e){for(var i=e.data.likes,t=0;t<r.length;t++){var n=r[t].attributes["data-id"].value,o=1===r.length?0:n-1,a=document.getElementById("j-aime-".concat(n)),c=document.getElementById("j-aimePas-".concat(n));switch(a.innerHTML=i[o].countLikes,c.innerHTML=i[o].countDisLikes,i[o].aimeOuPas){case!0:a.previousSibling.previousSibling.style.color="#0a58ca";break;case!1:c.previousSibling.previousSibling.style.color="red";break;case null:a.previousSibling.previousSibling.style.color="#212529",c.previousSibling.previousSibling.style.color="#212529"}}})).catch((function(e){}))}var d=function(e,i,t){var n=e.parentElement.parentElement.children[0].children[0];n.innerHTML=i,n.style.setProperty("--popAfterLeft",t+"px"),n.classList.toggle("show"),(0,o.setTimeout)((function(){n.innerHTML="",n.classList.remove("show")}),3e3)};t(59038)},59038:(e,i,t)=>{t(91038),t(78783);var n=document.querySelectorAll(".page-item");onload=function(){Array.from(n,(function(e){e.attributes["page-id"]}))}}},e=>{e.O(0,[25,218],(()=>{return i=5134,e(e.s=i);var i}));e.O()}]);