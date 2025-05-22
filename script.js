document.addEventListener('DOMContentLoaded', () => {
    // Contact form validation
    const contactForm = document.getElementById('contact-form');
    const formMessage = document.getElementById('form-message');

    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();

            if (name && email && message) {
                formMessage.innerHTML = '<div class="alert alert-success">Message sent successfully!</div>';
                contactForm.reset();
            } else {
                formMessage.innerHTML = '<div class="alert alert-danger">Please fill out all fields.</div>';
            }
        });
    }
});