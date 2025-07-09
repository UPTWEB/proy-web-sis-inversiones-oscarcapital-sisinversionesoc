export function addInputErrors(inputItem, iconItem) {
    inputItem.classList.add("error");
    iconItem.classList.add("error");
}

export function clearInputErrors(inputItem, iconItem) {
    inputItem.classList.remove("error");
    iconItem.classList.remove("error");
}
