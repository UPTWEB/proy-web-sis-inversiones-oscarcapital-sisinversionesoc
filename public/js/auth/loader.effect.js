// btnItem = main button
// loaderItem = loader element on the main button
// btnText = text element on the main button
// bgColor = background color of the main button

export function showLoader(btnItem,loaderItem,btnText,bgColor) {
    btnText.style.display = 'none';
    loaderItem.hidden = false;
    btnItem.style.cursor = 'wait';
    btnItem.style.backgroundColor = bgColor;
    btnItem.disabled = true;
}


export function hideLoader(btnItem,loaderItem,btnText) {
    btnText.style.display = '';
    loaderItem.hidden = true;
    btnItem.style.removeProperty('background-color');
    btnItem.style.cursor = 'pointer';
    btnItem.disabled = false;
}