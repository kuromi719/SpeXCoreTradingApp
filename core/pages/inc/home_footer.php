    <?php 
        if (!empty(FetchWebSettings()['tawk_to'])) {
            echo FetchWebSettings()['tawk_to'];
        }
    ?>

    <script>
        function triggerConfettiEffect() {
            confetti({
                particleCount: 150,
                spread: 70,
                origin: { y: 0.3 },
                colors: ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeaa7', '#dda0dd', '#98d8c8']
            });
        }
    </script>

    <script>window.gtranslateSettings = {"default_language":"en","wrapper_selector":".gtranslate_wrapper"}</script>
    <script src="https://cdn.gtranslate.net/widgets/latest/popup.js" defer></script> 

    <script src="core/pages/assets/npm/gsap%403.12.5/dist/gsap.min.js"></script>
    <script src="core/pages/assets/npm/gsap%403.12.5/dist/ScrollTrigger.min.js"></script>

    <script src="core/pages/assets/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="core/pages/assets/ajax/libs/popper.js/2.11.8/umd/popper.min.js"></script>

    <script src="core/pages/assets/js/particle-canvas.js"></script>

    <script src="core/pages/assets/bootstrap/js/bootstrap.js"></script>
    <script src="core/pages/assets/js/main.js"></script>

</body>

</html>