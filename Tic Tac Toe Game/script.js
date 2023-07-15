let btnRef =document.querySelectorAll(".button-option");
let popupRef =document.querySelector(".popup");
let newgameBtn = document.getElementById("new-game");
let restartBtn = document.getElementById("restart");
let msgRef = document.getElementById("message");
// Winnig Pattern Array
let winningPattern =[
    [0, 1, 2],
    [0, 3, 6],
    [2, 5, 8],
    [6, 7, 8],
    [3, 4, 5],
    [1, 4, 7],
    [0, 4, 8],
    [2, 4, 6],
];

// Player 'X' plays First
let xTurn = true;
let count = 0;

// Disable all Button 
const disableButtons = () => {
    btnRef.forEach((element) => (element.disabled = true));

    popupRef.classList.remove("hide");
};

// Enable all button (for new Game and restart )  
const enableButoons = () => {
    btnRef.forEach((element) => {
        element.innerText = "";
        element.disabled = false;
    });

    popupRef.classList.add("hide");
}

// This function is executed when a player wins 
const widFunction = (letter) => {
    disableButtons();
    if (letter == "X") {
        msgRef.innerHTML = "&#x1F389; <br> 'X' Wins";
    } else {
        msgRef.innerHTML = "&#x1F389; <br> 'O' Wins"; 
    }
};

// Function for drow 

const drowFunction = () => {
    disableButtons();
    msgRef.innerHTML = "&#x1F389; <br> It's a Draw";
};

// New Game 
newgameBtn.addEventListener("click", () => {
    count = 0;
    enableButoons();
});
restartBtn.addEventListener("click", () => {
    count = 0;
    enableButoons();
});

// Display X/O on Click 
btnRef.forEach((element) => {
    element.addEventListener("click", () => {
        if (xTurn) {
            xTurn = false;
            element.innerText = "X";
            element.disabled = true;
        } else {
            xTurn = true;
            element.innerText = "O";
            element.disabled = true;
        }

        count +=1;
        if(count == 9){
            drowFunction();
        }

        winChecker();


    });
});

window.onload = enableButoons;


// Win Logic 
const winChecker = () =>{
    for (let i of winningPattern) {
        let [element1, element2, element3] = [
        btnRef[i[0]].innerText,
        btnRef[i[1]].innerText,
        btnRef[i[2]].innerText,
    ];

    if (element1!= "" && (element2 != "") & (element3 != "")){
        if(element1 == element2 && element2 ==element3){
            widFunction(element1);
        }
    }
    }
};