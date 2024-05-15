export function getData(fetchFrom) {
  return fetch(fetchFrom).then((res) => {
    if (!res.redirected) {
      return res.json();
    }
  });
}
export function getEncodedData(fetchFrom) {
  return fetch(fetchFrom).then((res) => {
    if (!res.redirected) {
      return res.blob();
    }
  });
}
export function postData(data, postTo) {
  return fetch(postTo, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  }).then((res) => {
    if (!res.redirected) {
      return res.json();
    }
  });
}
export function postEncodedData(data, postTo) {
  return fetch(postTo, {
    method: "POST",
    body: data,
  }).then((res) => {
    if (!res.redirected) {
      return res.text();
    }
  });
}
export function patchData(data, patchTo) {
  return fetch(patchTo, {
    method: "PATCH",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  }).then((res) => {
    if (!res.redirected) {
      return res.json();
    }
  });
}
export function patchEncodedData(data, patchTo) {
  return fetch(patchTo, {
    method: "PATCH",
    body: data,
  }).then((res) => res.text());
}
export function deleteData(deleteFrom) {
  return fetch(deleteFrom, {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
    },
  }).then((res) => {
    if (!res.redirected) {
      return res.json();
    }
    else{
      window.location.reload();
    }
  });
}
