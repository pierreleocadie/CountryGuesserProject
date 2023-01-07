
let params = (new URL(document.location)).searchParams;
var serverDataReceived;
var playerDataLoaded = {
    player_id: null,
    nickname: null,
    email: null,
    credential: null
};
const countriesToGuess = ["Fr", "It", "Sp", "Gr", "Po", "Sw", "Au", "Be", "Bu", "Ch", "De", "Du", "En", "Es", "Fi", "Gr", "Ho", "Ir", "Is", "Li", "Ma", "No", "Po", "Ro", "Ru", "Sc", "Se", "Si", "Sw", "Tu", "Uk", "Us"];
var ws;
const loginAPI = "https://api.countryguesser.deletesystem32.fr/login";
const registerAPI = "https://api.countryguesser.deletesystem32.fr/register";
const loginSection = document.getElementById('login');
const registerSection = document.getElementById('register');
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const home = document.getElementById('home');
const playerData = document.getElementById('playerData');
const searchMultiplayerGame = document.getElementById('searchMultiplayerGame');
const cancelMultiplayerGame = document.getElementById('cancelMultiplayerGame');
const roomSize = document.getElementById('roomSize');
const playerResponse = document.getElementById('playerResponse');
const sendPlayerResponse = document.getElementById('sendPlayerResponse');
const lastRoundData = document.getElementById('lastRoundData');
const roundData = document.getElementById('roundData');
const maxRounds = document.getElementById('maxRounds');

// Hide by default
home.style.display = 'none';
cancelMultiplayerGame.style.display = 'none';
playerResponse.style.display = 'none';
sendPlayerResponse.style.display = 'none';

// Register form
registerForm.addEventListener('submit', function(event) {
    event.preventDefault();
    let formData = new FormData(registerForm);
    console.log(formData);
    let data = {
        nickname: formData.get('nickname'),
        email: formData.get('email'),
        password: formData.get('password'),
        password_confirmation: formData.get('password_confirmation')
    }
    fetch(registerAPI, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        if ('credential' in data) {
            playerDataLoaded = data;
            console.log(playerDataLoaded);
            // If the registration is successful, we can hide the register form and the login form and store the player data in a cookie
            registerSection.style.display = 'none';
            loginSection.style.display = 'none';
            cookieValue = `player_id=${data.player_id};nickname=${data.nickname};email=${data.email};credential=${data.credential}`;
            localStorage.setItem('playerData', cookieValue);
            // We can also show the home page
            home.style.display = 'block';
            // And display the player data
            playerData.innerHTML = `Player ID: ${data.player_id} <br> Player nickname: ${data.nickname} <br> Player email: ${data.email} <br> Player credential: ${data.credential}`;
        } else {
            // If the registration is not successful, we can display an error message
            document.getElementById('registerError').innerHTML = data.message;
        }
    })
})

// Login form
loginForm.addEventListener('submit', function(event) {
    event.preventDefault();
    let formData = new FormData(loginForm);
    let data = {
        nickname_email: formData.get('nickname_email'),
        password: formData.get('password')
    }
    console.log(data);
    fetch(loginAPI, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        if ('credential' in data) {
            playerDataLoaded = data;
            console.log(playerDataLoaded);
            // If the login is successful, we can hide the login form and the register form and store the player data in a cookie
            loginSection.style.display = 'none';
            registerSection.style.display = 'none';
            cookieValue = `player_id=${data.player_id};nickname=${data.nickname};email=${data.email};credential=${data.credential}`;
            localStorage.setItem('playerData', cookieValue);
            // We can also show the home page
            home.style.display = 'block';
            // And display the player data
            playerData.innerHTML = `Player ID: ${data.player_id} <br> Player nickname: ${data.nickname} <br> Player email: ${data.email} <br> Player credential: ${data.credential}`;
        } else {
            // If the login is not successful, we can display an error message
            document.getElementById('loginError').innerHTML = data.message;
        }
    })
})

// Check if a cookie is set with the players information
if (localStorage.getItem('playerData') != null) {
    let playerDataStored = localStorage.getItem('playerData');
    playerDataLoaded = {
        player_id: playerDataStored.split(';')[0].split('=')[1],
        nickname: playerDataStored.split(';')[1].split('=')[1],
        email: playerDataStored.split(';')[2].split('=')[1],
        credential: playerDataStored.split(';')[3].split('=')[1]
    }
    // If the cookie is set, the player is logged in so we can hide the login form and the register form
    loginSection.style.display = 'none';
    registerSection.style.display = 'none';
    // We can also show the home page
    home.style.display = 'block';
    cancelMultiplayerGame.style.display = 'none';
    // And display the player data
    playerData.innerHTML = `Player ID: ${playerDataLoaded.player_id} <br> Player nickname: ${playerDataLoaded.nickname} <br> Player email: ${playerDataLoaded.email} <br> Player credential: ${playerDataLoaded.credential}`;
}

// When the player clicks on the "search multiplayer game" button
searchMultiplayerGame.addEventListener('click', function(event) {
    event.preventDefault();
    // We can hide the "search multiplayer game" button
    searchMultiplayerGame.style.display = 'none';
    // And show the "cancel multiplayer game" button
    cancelMultiplayerGame.style.display = 'block';
    playerResponse.style.display = 'none';
    sendPlayerResponse.style.display = 'none';
    roomSize.style.display = 'none';
    maxRounds.style.display = 'none';
    // We can also create a websocket connection
    ws = new WebSocket('ws://localhost:7777?playerCredential=' + playerDataLoaded.credential + '&roomSize=' + parseInt(roomSize.value) + '&maxRounds=' + parseInt(maxRounds.value));
    // Send a message a random country to guess for the game to the server
    ws.onopen = () => {
        ws.send(JSON.stringify({
            type: "roundData",
            name: "test",
            code: countriesToGuess[Math.floor(Math.random() * countriesToGuess.length)],
            flag: "flagPays",
            latLng: "latLngPays",
        }));
    }
    ws.onmessage = response => {
        console.log(response);
        serverDataReceived = JSON.parse(response.data);
        console.log(serverDataReceived);
        if(serverDataReceived.type == "data") {
            // If the server sends a message, we can display it
            document.getElementById('serverMessage').innerHTML = "<p>ROOM ID : " + serverDataReceived.roomId + "\n\nROOM SIZE : " + serverDataReceived.roomSize + "\n\nGAME ID : " + serverDataReceived.gameId + "</p>";
            playerResponse.style.display = 'block';
            sendPlayerResponse.style.display = 'block';
        }else if(serverDataReceived.type == "information" && serverDataReceived.informationType == "roomCreated"){
            // If the server sends a message, we can display it
            document.getElementById('serverMessage').innerHTML = serverDataReceived.message;
        }else if(serverDataReceived.type == "information" && serverDataReceived.informationType == "inQueue") {
            // If the server sends a message, we can display it
            document.getElementById('serverMessage').innerHTML = serverDataReceived.message;
        }else if(serverDataReceived.type == "error" && serverDataReceived.errorType == "aPlayerLeft") {
            // If the server sends a message, we can display it
            document.getElementById('serverMessage').innerHTML = serverDataReceived.message;
            // We can show the input for the room size
            roomSize.style.display = 'block';
            maxRounds.style.display = 'block';
            // We can hide the "cancel multiplayer game" button
            cancelMultiplayerGame.style.display = 'none';
            playerResponse.style.display = 'none';
            sendPlayerResponse.style.display = 'none';
            // And show the "search multiplayer game" button
            searchMultiplayerGame.style.display = 'block';
            // Clear the round data section
            document.getElementById('roundData').innerHTML = "";
            // We can also close the websocket connection
            ws.close();
        }else if(serverDataReceived.type == "information" && serverDataReceived.informationType == "roundCreated") {
            // If the server sends a message, we can display it
            document.getElementById('roundData').innerHTML = "<p>ROUND ID : " + serverDataReceived.roundId + "</p>";
        }else if(serverDataReceived.type == "information" && serverDataReceived.informationType == "roundOver" && serverDataReceived.nextRoundId > 1) {
            // Send a new country to guess for the next round to the server
            ws.send(JSON.stringify({
                type: "roundData",
                name: "test",
                code: countriesToGuess[Math.floor(Math.random() * countriesToGuess.length)],
                flag: "flagPays",
                latLng: "latLngPays",
            }));
        }
    }
})

// When the player clicks on the "send player response" button
sendPlayerResponse.addEventListener('click', function(event) {
    event.preventDefault();
    // We can send the player response to the server
    ws.send(JSON.stringify({
        type: "playerResponse",
        playerResponse: playerResponse.value
    }));
    ws.onmessage = response => {
        serverDataReceived = JSON.parse(response.data);
        console.log(serverDataReceived);
        if(serverDataReceived.type == "information" && serverDataReceived.informationType == "roundOver" && serverDataReceived.nextRoundId > 1) {
            // Send a new country to guess for the next round to the server
            ws.send(JSON.stringify({
                type: "roundData",
                name: "test",
                code: countriesToGuess[Math.floor(Math.random() * countriesToGuess.length)],
                flag: "flagPays",
                latLng: "latLngPays",
            }));
        }else if(serverDataReceived.type == "information" && serverDataReceived.informationType == "roundCreated") {
            // If the server sends a message, we can display it
            document.getElementById('roundData').innerHTML = "<p>ROUND ID : " + serverDataReceived.roundId + "</p>";
        }else if(serverDataReceived.type == "information" && serverDataReceived.informationType == "gameOver") {
            // If the server sends a message, we can display it
            document.getElementById('serverMessage').innerHTML = serverDataReceived.message;
            // We can show the input for the room size
            roomSize.style.display = 'block';
            maxRounds.style.display = 'block';
            // We can hide the "cancel multiplayer game" button
            cancelMultiplayerGame.style.display = 'none';
            playerResponse.style.display = 'none';
            sendPlayerResponse.style.display = 'none';
            // And show the "search multiplayer game" button
            searchMultiplayerGame.style.display = 'block';
            // We can also close the websocket connection
            ws.close();
        }else if(serverDataReceived.type == "error" && serverDataReceived.errorType == "aPlayerLeft") {
            // If the server sends a message, we can display it
            document.getElementById('serverMessage').innerHTML = serverDataReceived.message;
            // We can show the input for the room size
            roomSize.style.display = 'block';
            maxRounds.style.display = 'block';
            // We can hide the "cancel multiplayer game" button
            cancelMultiplayerGame.style.display = 'none';
            playerResponse.style.display = 'none';
            sendPlayerResponse.style.display = 'none';
            // And show the "search multiplayer game" button
            searchMultiplayerGame.style.display = 'block';
            // Clear the round data section
            document.getElementById('roundData').innerHTML = "";
            // We can also close the websocket connection
            ws.close();
        }
    }
})

// When the player clicks on the "cancel multiplayer game" button
cancelMultiplayerGame.addEventListener('click', function(event) {
    event.preventDefault();
    // We can hide the "cancel multiplayer game" button
    cancelMultiplayerGame.style.display = 'none';
    // And show the "search multiplayer game" button
    searchMultiplayerGame.style.display = 'block';
    // We can show the input for the room size
    roomSize.style.display = 'block';
    maxRounds.style.display = 'block';
    playerResponse.style.display = 'none';
    sendPlayerResponse.style.display = 'none';
    // Send a message to the server to cancel the multiplayer game
    ws.send(JSON.stringify({
        type: "cancelMultiplayerGame"
    }));
    ws.onmessage = response => {
        serverDataReceived = JSON.parse(response.data);
        console.log(serverDataReceived);
        if(serverDataReceived.type == "information" && (serverDataReceived.informationType == "removedFromQueue" || serverDataReceived.informationType == "removedFromRoom")) {
            // If the server sends a message, we can display it
            document.getElementById('serverMessage').innerHTML = serverDataReceived.message;
            // We can also close the websocket connection
            ws.close();
        }
    }
})

// When the player clicks on the "logout" button
logout.addEventListener('click', function(event) {
    event.preventDefault();
    // We can hide the home page
    home.style.display = 'none';
    // And show the login form and the register form
    loginSection.style.display = 'block';
    registerSection.style.display = 'block';
    // We can also delete the cookie
    localStorage.removeItem('playerData');
})