export function getData(fetchFrom) {
  return fetch(fetchFrom).then((res) => res.json());
}
export function getEncodedData(fetchFrom) {
  return fetch(fetchFrom).then((res) => res.blob());
}
export function postData(data, postTo) {
  return fetch(postTo, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  }).then((res) => res.json());
}
export function postEncodedData(data, postTo) {
  return fetch(postTo, {
    method: "POST",
    body: data,
  }).then((res) => res.text());
}
