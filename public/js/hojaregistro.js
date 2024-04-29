const $ = document;

ruc = $.getElementById("ruc");

ruc.keydown = (e) => {
    if (e.key === "Enter") {
        console.log("Enter key pressed");
    }
}

/*const requestOptions = {
  method: "GET",
  redirect: "follow"
};

fetch("https://dniruc.apisperu.com/api/v1/ruc/20504898173?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImNhYXJyb3lvQGhvdG1haWwuY29tIn0.8qOPsmbIXb6G5eTo1OQ8CJXKDisde7LItI2faTRSeoE", requestOptions)
  .then((response) => response.text())
  .then((result) => console.log(result))
  .catch((error) => console.error(error));*/