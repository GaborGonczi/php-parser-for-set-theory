import { getData } from "../utils.js";
import { CONSTANTS } from "../constants.js";
const container=document.querySelector('#container');
function loadData(){
   container.innerHTML="";
   getData(CONSTANTS.crudUrl+'&automatons/get').then(data=>{
        if(data.automatons.length>0){
            let trs=data.automatons.map(elem=>{
                return`<tr>
                <td>${elem.id}</td>
                <td>${elem.expression_id}</td>
                <td><a href=\"${elem.path}\">${elem.statement}</a></td>
                <td>${elem.created_at}</td>
                <td> ${elem.modified_at!==null?elem.modified_at:""}</td>
                <td>${elem.deleted_at!==null?elem.deleted_at:`<a href=\"${CONSTANTS.crudUrl+'&automatons/delete/'+elem.id}\">Törlés</a>`}</td>
                </tr>`
            }).join("");
            container.innerHTML=`<table>${trs}</table>`;
        }
        else
        {
            container.innerHTML=data.messages.noDiagrams;
        }   
   });
}
window.addEventListener("DOMContentLoaded",loadData);