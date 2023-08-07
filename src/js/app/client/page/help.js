import { CONSTANTS } from "../constants.js";

const backbtn=document.getElementById("back");
backbtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.serverUrl+"index.php";
})