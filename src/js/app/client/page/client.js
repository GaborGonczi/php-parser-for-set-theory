import { CONSTANTS } from "../constants.js";

const programbtn=document.getElementById("program");
const helpbtn=document.getElementById("help");
const surveybtn=document.getElementById("questionnaire");
const logoutbtn=document.getElementById('logout');
programbtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.programUrl;
});
helpbtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.helpUrl;
});
surveybtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.questionnaire;
});
logoutbtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.logoutUrl;
})