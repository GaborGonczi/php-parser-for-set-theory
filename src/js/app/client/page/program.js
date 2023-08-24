import { CONSTANTS } from "../constants.js"
import { htmlEntityMap } from "../htmlentitytable.js";
const backbtn=document.getElementById('back');
const downloadbtn=document.getElementById('download');
const printbtn=document.getElementById('print');
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
    return fetch(postTo,{
        method:'POST',
        body:data
    }).then(res=>res.text())
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
    let statement=String(inputField.value.substr(start,end));
    Object.keys(htmlEntityMap).forEach((entity)=>{
        statement.replaceAll(entity,htmlEntityMap[entity]);
    })
    let data={statement:statement,start:start, end:end,noparse:noparse,beforelogs:logs};
    postData(data,CONSTANTS.parseUrl).then(data=>{
        fillTemplate(data)
        inputField.value=data.json.map(r=>r.statement.trim()).join("\n");
        if(end===inputField.value.length&&end>0) inputField.value+="\n";

    })

}
function insertspecialcharacter(e){
    inputField.value+=e.target.value;
    inputField.focus();
}
function saveToFile(){
    getData(CONSTANTS.saveUrl).then(data=>{
        const blob= new Blob([JSON.stringify(data)],{type:"application/json"});
        const url=URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download="worksheet.json";
        link.click()

    })
}
function loadFromFile(){
    const load=loadfile.files[0];
    const formData = new FormData();
    formData.append("load", load);
    postEncodedData(formData,CONSTANTS.loadUrl).then(data=>{
        if(data){
            data=JSON.parse(data)
            alert(data["error"])
            return;
        }
        getData(CONSTANTS.parseUrl).then(data=>{
            inputField.value=data.json.map(r=>r.statement).join("\n");
            if(inputField.value.length>0&&inputField.value[inputField.value.length-1]!=='\n') inputField.value+="\n";
            fillTemplate(data)
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
      type:e.type || null,
      button:e.button || null,
      ctrlKey:e.ctrlKey || null,
      key:e.key || null,
      sourceElementId:e.target.id || null,
      sourceElementValue:e.target.value || null,
      sourceElementTagname:e.target.tagName || null,
      sourceElementTitle:e.target.title || null,
      time:`${year}/${month}/${day}-${hour}:${min}:${sec}:${milisec}` || null  
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
        if(inputField.value.length>0&&inputField.value[inputField.value.length-1]!=='\n') inputField.value+="\n";
        fillTemplate(data)
    })
    
}
backbtn.addEventListener("click",e=>{
    window.location.href=CONSTANTS.serverUrl+"index.php";
})
downloadbtn.addEventListener("click",saveToFile);
printbtn.addEventListener('click',e=>{
    window.print();
})
loadfile.addEventListener("change",loadFromFile)
window.addEventListener("DOMContentLoaded",load)