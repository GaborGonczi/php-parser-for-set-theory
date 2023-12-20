import { CONSTANTS } from "../constants.js";


/// Get the HTML element for the back button.

const backbtn=document.getElementById("back");


/// Add a click event listener to the back button that redirects to the index page

backbtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.serverUrl+"index.php";
})