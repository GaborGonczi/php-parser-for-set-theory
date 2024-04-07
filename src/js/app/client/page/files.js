import { deleteData, getData, patchData, postData } from "../utils.js";
import { CONSTANTS } from "../constants.js";
const container = document.querySelector("#container");
const message = document.querySelector("#message");
const myFilesContainer = document.querySelector("#my");
const othersFilesContainer = document.querySelector("#others");
function loadData() {
  container.innerHTML = "";

  if (localStorage.getItem("message") !== null) {
    message.innerHTML = JSON.parse(localStorage.getItem("message"))["message"];
    localStorage.removeItem("message");
  }
  getData(CONSTANTS.crudUrl + "&files/get").then((data) => {
    if (data.files.my.length > 0) {
        let div=document.createElement('div');
        div.innerHTML=`<table>${data.files.my
            .map((elem) => {
                return `<tr>
                            <td>${elem.id}</td>
                            <td>${elem.created_at}</td>
                            <td>${elem.modified_at}</td>
                            <td><a href=\"${CONSTANTS.loadUrl + `&id=${elem.id}`}\"><button>Módosítás</button></a></td>
                            <td><label>Mintafájl: <input data-template="${elem.id}" type="checkbox" ${elem.example ? "checked" : ""}/></label></td>
                            <td><button data-delete="${elem.id}" type="submit">Törlés</button></td>
                        </tr>`;
            }).join("")}</table>`;
        myFilesContainer.appendChild(div.firstElementChild);
    }
    if (data.files.shared.length > 0) {
        let div=document.createElement('div');
        div.innerHTML=`<table>${data.files.shared
            .map((elem) => {
                return `<tr>
                            <td>${elem.id}</td>
                            <td>${elem.created_at}</td>
                            <td>${elem.modified_at}</td>
                            <td><button data-copy="${elem.id}" type="submit">Másolás</button></td>
                            <td><label>Mintafájl: <input data-template="${elem.id}" type="checkbox" ${elem.example ? "checked" : ""} disabled/></label></td>
                            <td><button data-delete="${elem.id}" type="submit" disabled>Törlés</button></td>
                        </tr>`;
            })
            .join("")}</table>`;
        othersFilesContainer.appendChild(div.firstElementChild);
    
    }
    if (
        data.files.my.length > 0 ||
      data.files.shared.length > 0
    ) {
      container.appendChild(myFilesContainer);
      container.appendChild(othersFilesContainer);
      container.classList.toggle('hide');
      document.querySelectorAll("input[data-template]").forEach((elem) => {
        elem.addEventListener("change", (e) => {
          let id = e.target.dataset.template;
          let example = Boolean(e.target.checked);
          let data = { id: id, example: example };
          patchData(data, CONSTANTS.crudUrl + `&files/${id}`).then((data) => {
            localStorage.setItem("message", JSON.stringify(data));
            window.location.reload();
          });
        });
      });
      document.querySelectorAll("button[data-delete]").forEach((elem) => {
        elem.addEventListener("click", (e) => {
          let id = e.target.dataset.delete;
          deleteData(CONSTANTS.crudUrl + `&files/${id}`).then((data) => {
            localStorage.setItem("message", JSON.stringify(data));
            window.location.reload();
          });
        });
      });
      document.querySelectorAll("button[data-copy]").forEach((elem) => {
        elem.addEventListener("click", (e) => {
          let id = e.target.dataset.copy;
          postData(null, CONSTANTS.crudUrl + `&files/${id}`).then((data) => {
            localStorage.setItem("message", JSON.stringify(data));
            window.location.reload();
          });
        });
      });
    } else {
      message.innerHTML = data.messages.noFiles;
    }
  });
  localStorage.removeItem("langstrings");
}
window.addEventListener("DOMContentLoaded", loadData);
