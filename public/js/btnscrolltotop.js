// public/js/app.js or your main JS file

// naviagte top button
    document.addEventListener("DOMContentLoaded", function() {
        // Get the button
        let mybutton = document.getElementById("scrollToTopBtn");

        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {
            scrollFunction()
        };

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                mybutton.classList.remove('hidden');
            } else {
                mybutton.classList.add('hidden');
            }
        }

        // When the user clicks on the button, scroll to the top of the document with smooth behavior
        mybutton.addEventListener("click", function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });

