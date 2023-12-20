import { CONSTANTS } from "../constants.js";


/// Get the HTML elements for the buttons

const programbtn=document.getElementById("program");
const helpbtn=document.getElementById("help");
const surveybtn=document.getElementById("questionnaire");
const logoutbtn=document.getElementById('logout');


/// Add a click event listener to the program button that redirects to the program URL

programbtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.programUrl;
});


/// Add a click event listener to the program button that redirects to the help URL

helpbtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.helpUrl;
});


///  Add a click event listener to the program button that redirects to the questionaire URL

surveybtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.questionnaire;
});


/// Add a click event listener to the program button that redirects to the logout URL

logoutbtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.logoutUrl;
})