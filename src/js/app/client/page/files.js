import { getData } from "../utils.js";
import { CONSTANTS } from "../constants.js";
const container=document.querySelector('#container');
function loadData(){
   container.innerHTML="";
   getData(CONSTANTS.crudUrl+'&files/get').then(data=>{
        if(data.length>0){
            let trs=data.map(elem=>{
                return`<tr>
                <td>${elem.id}</td>
                <td>${elem.created_at}</td>
                <td> <a href=\"${CONSTANTS.loadUrl+`&id=${elem.id}`}\">${elem.modified_at!==null?elem.modified_at:"Módosítás"}</a></td>
                <td>${elem.deleted_at!==null?elem.deleted_at:`<a href=\"${CONSTANTS.crudUrl+'&files/delete/'+elem.id}\">Törlés</a>`}</td>
                </tr>`
            }).join("");
            container.innerHTML=`<table>${trs}</table>`;
        }
        else
        {
            container.innerHTML="Nincsenek fájlok";
        }   
   });
}
window.addEventListener("DOMContentLoaded",loadData);