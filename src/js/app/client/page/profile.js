import {postData } from "../utils.js";
import { CONSTANTS } from "../constants.js";
const savebtn=document.querySelector('#save')
const backbtn=document.querySelector('#back')
const profileTitle=document.querySelector('#profileTitle')
const languageSelector=document.querySelector('#languageSelector')
const languageSelectorLabel=document.querySelector('#languageSelectorLabel')
const languageSelectorHunOption=document.querySelector('option[value=hun]')
const languageSelectorEngOption=document.querySelector('option[value=eng]')
const user=document.querySelector('#userNameString')
const message=document.querySelector('#message')
function load(){
   
   savebtn.addEventListener('click',e=>{
        e.preventDefault();
        postData(languageSelector.value,CONSTANTS.profileUrl).then(data=>{
            message.innerText=data['message'];
            languageSelectorLabel.innerText=data['languageSelectorLabel'];
            savebtn.value=data['savebtn'];
            backbtn.innerText=data['backbtn'];
            profileTitle.innerText=data['profileTitle'];
            user.innerText=data['userNameString'];
            languageSelectorHunOption.label=data['hunOption'];
            languageSelectorEngOption.label=data['engOption'];
        })
   })
}
window.addEventListener("DOMContentLoaded",load);