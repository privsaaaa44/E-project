window.onscroll = function() {myFunction()};
var navbar_sticky = document.getElementById("navbar_sticky");
var sticky = navbar_sticky.offsetTop;
var navbar_height = document.querySelector('.navbar').offsetHeight;
function myFunction() {
  if (window.pageYOffset >= sticky + navbar_height) {
    navbar_sticky.classList.add("sticky")
	document.body.style.paddingTop = navbar_height + 'px';
  } else {
    navbar_sticky.classList.remove("sticky");
	document.body.style.paddingTop = '0'
  }
}


$(document).ready(function () { 
            $('.dropdown').hover(function () { 
                $(this).addClass('show'); 
                $(this).find('.dropdown-menu').addClass('show'); 
            }, function () { 
                $(this).removeClass('show'); 
                $(this).find('.dropdown-menu').removeClass('show'); 
            }); 
        }); 


        // carousal js

const slides = document.querySelectorAll('.slide');
const nextBtn = document.querySelector('.next');
const prevBtn = document.querySelector('.prev');

let index = 0;

function showslide(i) {
  slides.forEach(slide => slide.classList.remove('active'));
  slides[i].classList.add('active');
}

// NEXT
nextBtn.addEventListener('click', () => {
  index = (index + 1) % slides.length;
  showslide(index);
});

// PREV (fixed negative bug)
prevBtn.addEventListener('click', () => {
  index = (index - 1 + slides.length) % slides.length;
  showslide(index);
});

// AUTO SLIDE
setInterval(() => {
  index = (index + 1) % slides.length;
  showslide(index);
}, 5000);

// init
 showslide(index);