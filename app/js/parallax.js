var images = document.querySelectorAll('.parallax__image');

function setTopOffset(image) {
  var imageHeight = image.offsetHeight;
  var containerHeight = image.parentNode.offsetHeight;
  var pageHeight = document.documentElement.clientHeight;
  var imageDistance = imageHeight - containerHeight;
  var containerTop = image.parentNode.getBoundingClientRect().top;
  var distancePercent = 0;
  var offsetTop = 0;

  if (containerTop >= pageHeight) {
    distancePercent = 0;
  } else if (containerTop <= -containerHeight) {
    distancePercent = 1;
  } else {
    distancePercent = (containerTop + containerHeight) / (pageHeight + containerHeight);
  }

  offsetTop = distancePercent * imageDistance * -1;
  image.style.transform = 'translate3d(0,' + offsetTop + 'px, 0)';
}

function updateImages() {
  [].forEach.call(images, setTopOffset);
}

function animate() {
  requestAnimationFrame(animate);
  updateImages();
}

animate();
