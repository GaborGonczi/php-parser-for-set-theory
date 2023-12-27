import { CONSTANTS } from "../constants.js";
const questionnaire=document.getElementById("questionnaire");
const backbtn=document.getElementById("back");
function submitQuestionnaire() {
    
}
questionnaire.addEventListener("submit",submitQuestionnaire)
backbtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.serverUrl+"index.php";
})
