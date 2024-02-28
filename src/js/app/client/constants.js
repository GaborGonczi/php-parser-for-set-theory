
/// Export an object that contains various constants for the URLs of the server and client.

//const BASE_URL="http://parserforsettheory.nhely.hu/";
const BASE_URL="http://localhost/php-parser-for-set-theory/";

export const CONSTANTS={
    serverUrl:BASE_URL,
    loadUrl:BASE_URL+"src/php/app/server/page/backendentry.php?page=load",
    saveUrl:BASE_URL+"src/php/app/server/page/backendentry.php?page=save",
    newUrl:BASE_URL+"src/php/app/server/page/backendentry.php?page=new",
    parseUrl:BASE_URL+"src/php/app/server/page/backendentry.php?page=parse",
    templateUrl:BASE_URL+"src/mustache/app/client/page/ui.mustache",
    questionnaireUrl:BASE_URL+"src/php/app/server/page/backendentry.php?page=questionnaire",
  
}