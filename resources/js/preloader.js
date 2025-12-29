/**
 * Preloader JavaScript
 * Handles page load preloader and header sticky functionality
 * Ensures preloader is hidden before allowing other loaders
 */

// Mark body as preloader active on page load
if (document.body) {
    document.body.classList.add('preloader-active');
}

window.onload = function () {
    // Preloader JS
    const getPreloaderId = document.getElementById("preloader");
    setTimeout(() => {
        if (getPreloaderId) {
            getPreloaderId.classList.add('hide');
            // Remove from DOM after animation
            setTimeout(() => {
                if (getPreloaderId) {
                    getPreloaderId.style.display = "none";
                    // Remove preloader-active class from body
                    document.body.classList.remove('preloader-active');
                    // Notify that preloader is hidden
                    window.dispatchEvent(new CustomEvent('preloaderHidden'));
                }
            }, 500);
        } else {
            // If preloader doesn't exist, remove the class immediately
            document.body.classList.remove('preloader-active');
        }
    }, 500);

    // Header Sticky
    const getHeaderId = document.getElementById("header-area");
    if (getHeaderId) {
        window.addEventListener("scroll", (event) => {
            const height = 150;
            const { scrollTop } = event.target.scrollingElement;
            document
                .querySelector("#header-area")
                .classList.toggle("sticky", scrollTop >= height);
        });
    }

    // Navbar Sticky
    const getNavbarId = document.getElementById("navbar");
    if (getNavbarId) {
        window.addEventListener("scroll", (event) => {
            const height = 150;
            const { scrollTop } = event.target.scrollingElement;
            document
                .querySelector("#navbar")
                .classList.toggle("sticky", scrollTop >= height);
        });
    }
};
