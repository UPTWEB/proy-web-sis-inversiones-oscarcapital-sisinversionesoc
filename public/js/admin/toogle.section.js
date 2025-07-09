// sidebar

const currentURI = window.location.pathname;
console.log(currentURI);


let sidebarToggleClass = document.querySelector(".sidebar-nav");
document.addEventListener('DOMContentLoaded', function () {

    if (sidebarToggleClass) {

        // load localStorage for select section
        //const selectedSection = localStorage.getItem('selectedSection');

        const links = sidebarToggleClass.querySelectorAll('a');

        links.forEach(link => {
            if (link.getAttribute('href') === currentURI) {
                link.classList.add('selected');
            } else {
                link.classList.remove('selected');
            }
        });
    }
});


// sidebarToggleClass.addEventListener("click", function (event) {
//     if (event.target.tagName === "A" || event.target.tagName === "LI") {
//         const targetButton = event.target.tagName === 'A' ? event.target : event.target.closest('a');
//         if (targetButton) {

//             localStorage.setItem('selectedSection', targetButton.getAttribute('href'));
//         }
//     }

// });
