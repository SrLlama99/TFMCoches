/**
 *  load cookies into easy access, READ ONLY, dict
 * @returns dict cookies
 */
function getCookie(k) {
    let cookies = {};
    document.cookie.split("; ").forEach((k) => {
        let [key, value] = k.split("=", 2);
        cookies[key] = value;
    })
    return cookies[k]
}
/**
 * Builds the hero section so newcomers understand the website.
 */
function buildHero() {
    let main = document.getElementById('mainContainer')
    let container = document.createElement('div');
    container.classList.add("customnav", "p-3", "my-2", "d-flex", "flex-column", "align-items-center", "text-center");
    let addToContainer = []

    let heading = document.createElement('h1')
    heading.innerText = "Welcome to The Fast Garage!"
    addToContainer.push(heading)

    let subtext = document.createElement('h3')
    subtext.innerText = "The repository for all things cars."
    subtext.classList.add("mb-4")
    addToContainer.push(subtext)

    
    let text = "In the bar you can lookup models by name."+"\n"+
    "If the car you're looking for doesn't exist, you can create it with the green + button!"+"\n\n"+
    "If you're logged in you can check your garage with the cars you've rated."
    let paragraph = document.createElement('p')
    paragraph.innerText = text
    addToContainer.push(paragraph)

    let dismiss = document.createElement('button')
    dismiss.innerText = "Got it!"
    dismiss.classList.add("customNavBtn", "text-white", "btn-success", "btn")
    dismiss.addEventListener("click", (ev) => {
        document.cookie = "onboarding=false";
        ev.target.parentElement.remove();
    })
    addToContainer.push(dismiss)

    addToContainer.forEach((elm) => {
        container.appendChild(elm)
    })

    main.prepend(container)
}

document.addEventListener("DOMContentLoaded", () => {
    switch (getCookie("onboarding")) {
        case "false":
            return;
            break;
        case undefined:
            document.cookie = "onboarding=true";
        case "true":
            buildHero()
            break;
        default:
            console.warn(getCookie("onboarding"))
            break;
    }
});
