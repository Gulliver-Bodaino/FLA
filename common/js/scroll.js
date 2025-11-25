document.addEventListener("DOMContentLoaded", function(event) {
  var scrollSpeed = 800;

  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();

      var targetId = this.getAttribute('href').substring(1);
      var targetSection = document.getElementById(targetId);

      if (targetSection) {
        var startPosition = window.pageYOffset;
        var targetPosition = targetSection.getBoundingClientRect().top + window.pageYOffset - 80;
        var distance = targetPosition - startPosition;
        var startTime = null;

        function scrollStep(timestamp){
          if (!startTime) startTime = timestamp;
          var progress = timestamp - startTime;
          var scrollY = easeInOutQuad(progress, startPosition, distance, scrollSpeed);
          window.scrollTo(0, scrollY);

          if (progress < scrollSpeed){
            requestAnimationFrame(scrollStep);
          }
        }

        function easeInOutQuad(t, b, c, d){
          t /= d / 2;
          if (t < 1) return c / 2 * t * t + b;
          t--;
          return -c / 2 * (t * (t - 2) - 1) + b;
        }

        requestAnimationFrame(scrollStep);
      }
    });
  });
});