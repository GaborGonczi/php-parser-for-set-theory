import { CONSTANTS } from "../constants.js"
const backbtn=document.getElementById("back");
const downloadbtn=document.getElementById("download");
const inputField=document.querySelector('#input textarea#text');
const opButtons=document.querySelectorAll('.operator');
const mode=document.querySelector('.switch input')
const loadfile=document.getElementById('load');
let logs=[]
function getData(fetchFrom){
   return fetch(fetchFrom)
    .then(res=>res.json())
}
function getEncodedData(fetchFrom){
    return fetch(fetchFrom)
    .then(res=>res.blob())
}
function postData(data,postTo){
    return fetch(postTo,{
        method:'POST',
        headers:{

          'Content-Type': 'application/json'
        },
        body:JSON.stringify(data)
    })
    .then(res=>res.json())
}
function postEncodedData(data,postTo){
    const blob=new Blob([data])
    return fetch(postTo,{
        method:'POST',
        headers:{

            'Content-Type': 'application/octet-stream'
        },
        body:blob
    }).then(res=>res.json())
}
function fillTemplate(data){
    return fetch(CONSTANTS.templateUrl)
    .then((response) => response.text())
    .then((template) => {
        const rendered = Mustache.render(template, data);
        document.getElementById('output').innerHTML = rendered; 
    })
}
function save(e){
    if(e.code!=="Enter"&&e.code!="NumpadEnter") return;
    let cursorPos=inputField.selectionStart;
    let start=inputField.value.lastIndexOf("\n",cursorPos)+1;
    let end=inputField.value.indexOf("\n",start)!==-1?inputField.value.indexOf("\n",start):inputField.value.length;
    let noparse=mode.checked
    let data={statement:inputField.value.substr(start,end),start:start, end:end,noparse:noparse,beforelogs:logs};
    postData(data,CONSTANTS.parseUrl).then(data=>{
        fillTemplate(data)
        inputField.value=data.json.map(r=>r.statement.trim()).join("\n");
        if(end===inputField.value.length) inputField.value+="\n";

    })

}
function insertspecialcharacter(e){
    inputField.value+=e.target.value;
    inputField.focus();
}
function saveToFile(){
    getEncodedData(CONSTANTS.saveUrl).then(data=>{
        const blob= new Blob([data]);
        const url=URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download="saved.jpfm";
        link.click()

    })
}
function loadFromFile(){
    const fileToSend=loadfile.files[0];
    fileToSend.arrayBuffer().then(data=>{
        postEncodedData(data,CONSTANTS.loadUrl).then(data=>{
            fillTemplate(data)
            inputField.value=data.json.map(r=>r.statement.trim()).join("\n");
            inputField.value+="\n";
        })
    })
}
function log(e){
    let now= new Date();
    let year=now.getFullYear();
    let month=now.getDate();
    let day=now.getDay();
    let hour=now.getHours();
    let min=now.getMinutes();
    let sec=now.getSeconds();
    let milisec=now.getMilliseconds();
    let eventData={
      type:e.type,
      button:e.button,
      ctrlKey:e.ctrlKey,
      key:e.key,
      sourceElementValue:e.target.value,
      sourceElementTagname:e.target.tagName,
      sourceElementTitle:e.target.title,
      time:`${year}/${month}/${day}-${hour}:${min}:${sec}:${milisec}`  
    }
    logs.push(eventData);
}
function setUpLog(){
    opButtons.forEach(btn=>{
        btn.addEventListener("click",log)
    })
    inputField.addEventListener("keydown",e=>{
        if(e.code!=="Enter"&&e.code!="NumpadEnter") return;
        log(e);
    })
    mode.addEventListener("change",log);
    backbtn.addEventListener("click",log)
    downloadbtn.addEventListener("click",log)
    loadfile.addEventListener("change",log)
}
function loadUi(){
    const container=document.querySelector(".button-grid-container")
    container.style.setProperty('--grid-column','3')
    container.style.setProperty('--grid-row',(opButtons.length/window.getComputedStyle(container).getPropertyValue('--grid-column'))+1);
    opButtons.forEach(btn=>{
        btn.addEventListener("click",insertspecialcharacter)
    })
    inputField.addEventListener("keydown",save)
}
function load(e){
    log(e)
    setUpLog()
    loadUi()
    getData(CONSTANTS.parseUrl).then(data=>{
        inputField.value=data.json.map(r=>r.statement).join("\n");
        if(inputField.value[inputField.value.length-1]!=='\n') inputField.value+="\n";
        fillTemplate(data)
    })
    
}
backbtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.serverUrl+"index.php";
})
downloadbtn.addEventListener("click",saveToFile);
loadfile.addEventListener("change",loadFromFile)
window.addEventListener("DOMContentLoaded",load)