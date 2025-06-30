<div class="flex flex-col">
    <!-- Fixed footer with navigation dots and arrows -->
    <div class="fixed bottom-0 left-0 right-0 flex justify-center items-center gap-2.5 p-2.5 bg-black ">
        <div id="left-arrow" class="arrow w-16 md:w-20 cursor-pointer">
            <img src="{{ asset('images/previous.png') }}" alt="Previous" class="w-4 h-4 ml-11" />
        </div>
        <div class="dot w-4 h-4 bg-transparent border border-white rounded-full transition duration-300 ease-in-out cursor-pointer"></div>
        <div class="dot w-4 h-4 bg-transparent border border-white rounded-full transition duration-300 ease-in-out cursor-pointer"></div>
        <div class="dot w-4 h-4 bg-transparent border border-white rounded-full transition duration-300 ease-in-out cursor-pointer"></div>
        {{-- <div class="dot w-4 h-4 bg-transparent border border-white rounded-full transition duration-300 ease-in-out cursor-pointer"></div> --}}
        {{-- <div class="dot w-4 h-4 bg-transparent border border-white rounded-full transition duration-300 ease-in-out cursor-pointer"></div> --}}
        <div id="right-arrow" class="arrow w-16 md:w-20 cursor-pointer">
            <img src="{{ asset('images/next.png') }}" alt="Next" class="w-4 h-4" />
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dots = document.querySelectorAll('.dot');
        const leftArrow = document.getElementById('left-arrow');
        const rightArrow = document.getElementById('right-arrow');

        // URLs for each dot
        const urls = [
            '{{ route('mobile.trainingday') }}',
            '{{ route('mobile.readinessscore') }}',
            '{{ route('mobile.workout') }}',
            // '{{ route('mobile.workouttimer') }}',
            // '{{ route('mobile.histroyview') }}',
           
        ];

        // Function to update dots based on active URL
        function updateDotsByUrl(url) {
            const index = urls.findIndex(u => u === url);
            if (index !== -1) {
                updateDots(index);
            }
        }

        // Function to update dot styles
        function updateDots(index) {
            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.classList.remove('bg-transparent');
                    dot.classList.add('bg-white');
                } else {
                    dot.classList.remove('bg-white');
                    dot.classList.add('bg-transparent');
                }
            });

            // Update arrows' visibility without hiding them
            leftArrow.style.opacity = (index === 0) ? '0.5' : '1';
            rightArrow.style.opacity = (index === dots.length - 1) ? '0.5' : '1';
            leftArrow.style.pointerEvents = (index === 0) ? 'none' : 'auto';
            rightArrow.style.pointerEvents = (index === dots.length - 1) ? 'none' : 'auto';
        }

        // Add click event listeners to dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', function () {
                updateDots(index);
                // Navigate to the corresponding URL
                window.location.href = urls[index];
            });
        });

        // Add click event listeners to arrows
        leftArrow.addEventListener('click', function () {
            const currentIndex = urls.findIndex(u => u === window.location.href);
            if (currentIndex > 0) {
                const newIndex = currentIndex - 1;
                updateDots(newIndex);
                window.location.href = urls[newIndex];
            }
        });

        rightArrow.addEventListener('click', function () {
            const currentIndex = urls.findIndex(u => u === window.location.href);
            if (currentIndex < urls.length - 1) {
                const newIndex = currentIndex + 1;
                updateDots(newIndex);
                window.location.href = urls[newIndex];
            }
        });

        // Initial update based on current URL
        updateDotsByUrl(window.location.href);
    });
</script>
