import { CONSTANTS } from "../constants.js";

const programbtn=document.getElementById("program");
const helpbtn=document.getElementById("help");
const logoutbtn=document.getElementById('logout');
programbtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.programUrl;
});
helpbtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.helpUrl;
});
logoutbtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.logoutUrl;
})