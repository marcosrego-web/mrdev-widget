function mrGetCookie(e){if(e){const t=e+"=",n=decodeURIComponent(document.cookie).split(";");for(id=0;id<n.length;id++){let e=n[id];for(;" "===e.charAt(0);)e=e.substring(1);if(0===e.indexOf(t))return e.substring(t.length,e.length)}return""}}function mrIsInView(e,t,n){if(e){void 0===t&&(t=window.screen.height),void 0===n&&(n=0);let o=e.getBoundingClientRect().top-t,r=e.getBoundingClientRect().top+e.offsetHeight-n;if(o<0&&r>0)return!0}}function mrScrollTo(e,t){e&&(element=document.scrollingElement||document.documentElement,start=element.scrollTop,change=e-start,startDate=+new Date,easeInOutQuad=function(e,t,n,o){return(e/=o/2)<1?n/2*e*e+t:-n/2*(--e*(e-2)-1)+t},animateScroll=function(){const n=+new Date-startDate;element.scrollTop=parseInt(easeInOutQuad(n,start,change,t)),n<t?requestAnimationFrame(animateScroll):element.scrollTop=e},animateScroll())}function mrParallax(e){if(e&&(e=document.querySelectorAll(e))&&!matchMedia("(prefers-reduced-motion: reduce)").matches)for(id=0;id<e.length;id++){const t=e[id];let n=t.getBoundingClientRect().top/6,o=Math.round(100*n)/100;t.style.backgroundPositionY=o+"px"}}document.addEventListener("click",function(e){e.target.matches(".mr-offcanvas-toggle")&&(document.querySelector(".mr-offcanvas-container").classList.remove("mr-hide"),document.querySelector(".mr-offcanvas-container").classList.toggle("active"),document.querySelector("body").classList.toggle("mr-noscroll"),document.querySelector(".mr-offcanvas.mr-transitionright .mr-offcanvas-container:not(.active)")&&(document.querySelector(".mr-offcanvas").classList.remove("mr-transitionright"),document.querySelector(".mr-offcanvas").classList.add("mr-transitionleft")),document.querySelector(".mr-offcanvas.mr-transitionleft .mr-offcanvas-container.active")&&(document.querySelector(".mr-offcanvas").classList.remove("mr-transitionleft"),document.querySelector(".mr-offcanvas").classList.add("mr-transitionright")))},!1);