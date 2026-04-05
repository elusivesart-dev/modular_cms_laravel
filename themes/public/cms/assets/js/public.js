document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.querySelector('.public-navbar');

    if (!navbar) {
        return;
    }

    const handleScrollState = () => {
        if (window.scrollY > 12) {
            navbar.classList.add('shadow');
        } else {
            navbar.classList.remove('shadow');
        }
    };

    handleScrollState();
    window.addEventListener('scroll', handleScrollState, { passive: true });
});