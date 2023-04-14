data.forEach((element, i) => 
{
    const main = document.querySelector(".main");
    const card = document.createElement('div');

    card.classList = "card";
    
    

    card.addEventListener("click", function() {
        window.open(data[i].link);
    })

    var btns="";
    if(data[i].button){
        data[i].button.forEach((tags)=> 
        {
            btns += `<button>${tags}</button>`;
        });
    }
    const viedoCard = `
    <div class="top">
        <div class="videoNumber">${data[i].no}</div>
        <img src="${data[i].image}" alt="img">
        <div class="timing">${data[i].date}</div>
    </div>
    <div class="middle">
        <p class="title">${data[i].title}</p>
        <p class="description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam rutrum, massa a
            semper rutrum wx tss.</p>
    </div>
    <div class="bottom">
        ${btns}
    </div>
    `  ;

    
    
    card.innerHTML += viedoCard;
    main.appendChild(card);
}); 
