document.addEventListener("scroll", () => {
    const scrollTop = window.scrollY;
    const form = document.querySelector('.form-container');
    const table = document.querySelector('.table-container');

    form.style.transform = `translateY(${scrollTop * 0.3}px)`;
    table.style.transform = `translateY(${scrollTop * 0.5}px)`;
});