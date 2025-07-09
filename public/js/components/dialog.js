async function toggleDialog(dialogId){
    // Verificar si el navegador soporta View Transitions API
    if (document.startViewTransition) {
        const viewTransitionClass = 'vt-element-animation';
        const viewTransitionClassClosing = 'vt-element-animation-closing';
        
        if(!dialogId){
            const openDialog = document.querySelector("dialog[open]");
            const originElement = document.querySelector("[origin-element]");
            
            openDialog.style.viewTransitionName = "vt-shared";
            openDialog.style.viewTransitionClass = viewTransitionClassClosing;

            const viewTransition = document.startViewTransition(() => {
                originElement.style.viewTransitionName = "vt-shared";
                originElement.style.viewTransitionClass = viewTransitionClassClosing;

                openDialog.style.viewTransitionName = "";
                openDialog.style.viewTransitionClass = "";

                openDialog.close();
            });

            await viewTransition.finished;
                originElement.style.viewTransitionName = "";
                originElement.style.viewTransitionClass = "";
                originElement.removeAttribute("origin-element");

            return false;
        }

        
        const dialog = document.getElementById(dialogId);
        const originElement = event.currentTarget;

        dialog.style.viewTransitionName = "vt-shared";
        dialog.style.viewTransitionClass = viewTransitionClass;

        originElement.style.viewTransitionName = "vt-shared";
        originElement.style.viewTransitionClass = viewTransitionClass;
        originElement.setAttribute("origin-element","");//bttn origen

        const viewTransition = document.startViewTransition(() => {
            originElement.style.viewTransitionName = "";
            originElement.style.viewTransitionClass = "";
            dialog.showModal();
        });

        await viewTransition.finished;
            dialog.style.viewTransitionName = "";
            dialog.style.viewTransitionClass = "";
    } else {
        // Alternativa para navegadores que no soportan View Transitions API
        if(!dialogId){
            const openDialog = document.querySelector("dialog[open]");
            const originElement = document.querySelector("[origin-element]");
            
            openDialog.close();
            originElement.removeAttribute("origin-element");
            return false;
        }
        
        const dialog = document.getElementById(dialogId);
        const originElement = event.currentTarget;
        
        originElement.setAttribute("origin-element","");
        dialog.showModal();
    }
}