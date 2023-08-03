const programbtn=document.getElementById("program");
const helpbtn=document.getElementById("help");
programbtn.addEventListener("click",e=>{
    window.location.href="./program.php";
});
helpbtn.addEventListener("click",e=>{
    window.location.href="./help.php";
});