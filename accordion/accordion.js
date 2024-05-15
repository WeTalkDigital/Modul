document.addEventListener('DOMContentLoaded', function() {
    const accordions = document.querySelectorAll('.custom-accordion .accordion-item');

    function closeAllExcept(activeAccordion) {
        accordions.forEach(accordion => {
            if (accordion !== activeAccordion) {
                const content = accordion.querySelector('.accordion-content');
                content.style.maxHeight = null;
                accordion.classList.remove('open');
            }
        });
    }

    accordions.forEach(accordion => {
        const title = accordion.querySelector('.accordion-title');
        const content = accordion.querySelector('.accordion-content');

        title.addEventListener('click', function() {
            const isOpen = accordion.classList.contains('open');

            // Zárjuk be az összes elemet, kivéve az aktívat
            closeAllExcept(isOpen ? null : accordion);

            if (!isOpen) {
                // Nyitjuk meg az elemet
                accordion.classList.add('open');
                content.style.maxHeight = content.scrollHeight + "px";
            } else {
                // Zárjuk be az elemet
                accordion.classList.remove('open');
                content.style.maxHeight = null;
            }
        });

        // Kezdeti állapot beállítása
        if (accordion.classList.contains('open')) {
            content.style.maxHeight = content.scrollHeight + "px";
        } else {
            content.style.maxHeight = null;
        }
    });
});