const handle = debounce(search, 500);
let endpoint = null;
let suggestionsPanel = null;
let url = '/marca'

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('navFormQuery').addEventListener('input', (e) => handle(e.target.value));
    document.getElementById('navForm').addEventListener('submit', (e) => handle(e.preventDefault()));
    endpoint = document.getElementsByName('queryEndpoint')[0].value;
    suggestionsPanel = document.getElementById('navFormSuggestionPanel');
});

function search(query) {
    suggestionsPanel.style.display = 'none';

    if (query == "") {
        return;
    }

    suggestionsPanel.innerHtml = query;
    // hit api
    fetch(endpoint + '/' + query, { method: 'POST' })
        .then(async (res) => {
            res = await res.json() // asynchronous js is the bane of my existance

            resultsHTML = []

            if (res.ok == false) {
                error = document.createElement('span')
                error.innerText = res.errorReason;
                error.classList.add("searchDropdownResult","d-inline");
                resultsHTML.push(error)

                createButton = document.createElement('a')
                createButton.innerText = "Create the model instead?";
                createButton.href = "/new/model"
                createButton.classList.add("btn", "btn-success", "customNavBtn", "text-white");
                resultsHTML.push(createButton)
            } else {
                res.data.forEach(car => {
                    carEl = document.createElement('a');
                    carEl.classList.add("searchDropdownResult");
                    carEl.href = `${url}/${car.marca}/${car.id}`;
                    carEl.title = car.nombre;

                    nombre = document.createElement('strong');
                    nombre.innerText = car.nombre + " "
                    carEl.appendChild(nombre)

                    marca = document.createElement('em');
                    marca.innerText = car.marca
                    carEl.appendChild(marca)

                    carEl.appendChild(document.createElement('br'));
                    resultsHTML.push(carEl)
                });
            }
            // exterminate children
            suggestionsPanel.replaceChildren()

            // update dom
            resultsHTML.forEach(el => {
                suggestionsPanel.appendChild(el);
            });
        })

    suggestionsPanel.style.display = 'block';
}

// https://www.youtube.com/watch?v=uomqVe6Y9Sg
function debounce(fn, delay) {
    let t;
    return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...args), delay)
    }
}