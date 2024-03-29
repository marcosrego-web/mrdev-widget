function mrwidMain(mrwidThis) {
  if (!mrwidThis.matches(".mr-item")) {
    mrwidThis = event.target.closest(".mr-item");
  }
  const mrwidLayout = mrwidThis.closest(".mr-layout");
  const mrwidPage = mrwidThis.parentNode;
  /*tabs*/
  const mrwidItemsTabs = mrwidLayout.querySelector(".mr-tabs.mr-items");
  const mrwidTabs = mrwidLayout.querySelector(".mr-tabs:not(.mr-items)");
  if (mrwidTabs && !mrwidLayout.classList.contains("mr-donotinactive")) {
    const mrtabs = mrwidTabs.querySelectorAll(".mr-tab");
    for (id = 0; id < mrtabs.length; id++) {
      const mrtab = mrtabs[id];
      mrtab.classList.remove("active", "inactive", "open");
    }
  }
  /*end*/
  if (mrwidThis.classList.contains("active")) {
    mrCloseActive(
      mrwidThis,
      mrwidLayout,
      mrwidPage /*tabs*/,
      mrwidItemsTabs /*end*/
    );
  } else if (!mrwidThis.classList.contains("active")) {
    mrChangeStatus(
      mrwidThis,
      mrwidLayout,
      mrwidPage /*tabs*/,
      mrwidItemsTabs /*end*/
    );
  }
  mrConfirmStatus(mrwidPage /*tabs*/, mrwidItemsTabs /*end*/);
}
/*itemoptions-remember*/
function mrCreateCookie(mrwidThis) {
  const Classes = mrwidThis.getAttribute("class");
  const firstClass = Classes.indexOf(" ");
  document.cookie =
    "mrRemember=" +
    Classes.substring(0, firstClass) +
    "; max-age=2592000; path=/";
}
/*end*/
function mrChangeStatus(
  mrwidThis,
  mrwidLayout,
  mrwidPage /*tabs*/,
  mrwidItemsTabs /*end*/
) {
  const mrwids = mrwidPage.children;
  for (id = 0; id < mrwids.length; id++) {
    /*globallayoutoptions-keepactive*/
    if (mrwidLayout.classList.contains("mr-keepactive")) {
      if (!mrwids[id].classList.contains("active")) {
        mrwids[id].classList.add("inactive");
      }
    } /*end*/ else if (!mrwidLayout.classList.contains("mr-keepactive")) {
      mrwids[id].classList.remove("active", "open", "mr-scroll");
      mrwids[id].classList.add("inactive");
    }
    /*tabs*/
    if (mrwidItemsTabs) {
      if (mrwidLayout.classList.contains("mr-keepactive")) {
        if (!mrwids[id].classList.contains("active")) {
          const mrwidTabId = mrwids[id].className.split(" ")[0];
          const mrwidTab = mrwidItemsTabs.querySelector(
            ".mr-tab." + mrwidTabId
          );
          if (mrwidTab) {
            mrwidTab.classList.add("inactive");
          }
        }
      } else if (!mrwidLayout.classList.contains("mr-keepactive")) {
        const mrwidTabId = mrwids[id].className.split(" ")[0];
        const mrwidTab = mrwidItemsTabs.querySelector(".mr-tab." + mrwidTabId);
        if (mrwidTab) {
          mrwidTab.classList.remove("active", "open", "mr-scroll");
          mrwidTab.classList.add("inactive");
        }
      }
    }
    /*end*/
  }
  mrwidThis.classList.remove("inactive");
  mrwidThis.classList.add("active");
  /*tabs*/
  if (mrwidItemsTabs) {
    const mrwidTabId = mrwidThis.className.split(" ")[0];
    const mrwidTab = mrwidItemsTabs.querySelector(".mr-tab." + mrwidTabId);
    if (mrwidTab) {
      mrwidTab.classList.remove("inactive");
      mrwidTab.classList.add("active");
    }
  }
  /*end*/
  /*itemoptions-autoscroll*/
  if (mrwidLayout.classList.contains("mr-autoscroll")) {
    let rect = mrwidThis.getBoundingClientRect();
    if (mrwidLayout.classList.contains("mr-windowheight")) {
      rect = mrwidPage.getBoundingClientRect();
    }
    const elementoffset = rect.top + window.pageYOffset;
    mrScrollTo(elementoffset, 750);
  }
  /*end*/
  /*itemoptions-url*/
  if (mrwidLayout.classList.contains("mr-url")) {
    history.pushState(
      "object or string",
      mrwidThis.querySelector(".mr-title").textContent,
      mrwidThis.getAttribute("url")
    );
  }
  /*end*/
  /*itemoptions-remember*/
  if (mrwidLayout.classList.contains("mr-remember")) {
    mrCreateCookie(mrwidThis);
  }
  /*end*/
  /*itemoptions-subitemactive*/
  if (mrwidLayout.classList.contains("mr-subitemactive")) {
    const mrwidSubCats = mrwidPage.querySelectorAll(
      ".mr-subitem.parent" + mrwidThis.classList[0]
    );
    if (mrwidSubCats) {
      for (id = 0; id < mrwidSubCats.length; id++) {
        mrwidSubCats[id].classList.add("mr-hide");
        if (
          mrwidSubCats[id].classList.contains("parent" + mrwidThis.classList[0])
        ) {
          mrwidSubCats[id].classList.remove("mr-hide");
          mrwidSubCats[id].classList.remove("inactive");
        }
      }
    }
  }
  /*end*/
}
function mrCloseActive(
  mrwidThis,
  mrwidLayout,
  mrwidPage /*tabs*/,
  mrwidItemsTabs /*end*/
) {
  if (!mrwidLayout.classList.contains("mr-donotinactive")) {
    mrwidThis.classList.remove("active", "open");
    mrwidThis.classList.add("inactive");
    /*tabs*/
    if (mrwidItemsTabs) {
      const mrwidTabId = mrwidThis.className.split(" ")[0];
      const mrwidTab = mrwidItemsTabs.querySelector(".mr-tab." + mrwidTabId);
      if (mrwidTab) {
        mrwidTab.classList.remove("active", "inactive", "open");
        mrwidTab.classList.add("inactive");
      }
    }
    /*end*/
  }
  if (
    mrwidLayout.classList.contains("mr-subitemactive") &&
    mrwidLayout.classList.contains("mr-hideinactives")
  ) {
    const mrwids = mrwidPage.children;
    if (mrwids) {
      for (id = 0; id < mrwids.length; id++) {
        if (mrwids[id].classList.contains("mr-subitem")) {
          mrwids[id].classList.add("mr-hide");
        }
        mrwids[id].classList.remove("active", "inactive", "open");
        /*tabs*/
        if (mrwidItemsTabs) {
          const mrwidTabId = mrwids[id].className.split(" ")[0];
          const mrwidTab = mrwidItemsTabs.querySelector(
            ".mr-tab." + mrwidTabId
          );
          if (mrwidTab) {
            mrwidTab.classList.remove("active", "inactive", "open");
          }
        }
        /*end*/
      }
    }
  }
}
function mrConfirmStatus(mrwidPage /*tabs*/, mrwidItemsTabs /*end*/) {
  const mrwidCheckState = mrwidPage.querySelectorAll(".active");
  if (!mrwidCheckState.length) {
    const mrwids = mrwidPage.children;
    for (id = 0; id < mrwids.length; id++) {
      mrwids[id].classList.remove("inactive");
    }
  }
  /*tabs*/
  if (mrwidItemsTabs) {
    const mrwidCheckState = mrwidItemsTabs.querySelectorAll(".mr-tab.active");
    if (!mrwidCheckState.length) {
      const mrtabs = mrwidItemsTabs.querySelectorAll(".mr-tab");
      if (mrtabs) {
        for (id = 0; id < mrtabs.length; id++) {
          mrtabs[id].classList.remove("inactive");
        }
      }
    }
  }
  /*end*/
}
/*perpage*/
function mrwidChangePage(currentElement, mrwidLayout, mrwidPage) {
  if (
    !!mrwidLayout.querySelector(
      '.mr-pageselect option[value="' + mrwidPage + '"]'
    )
  ) {
    const mrwidPageSelect = mrwidLayout.querySelector(".mr-pageselect");
    const mrwidRadios = mrwidLayout.querySelectorAll(".mr-radio");
    const mrwidCurrentRadio = mrwidLayout.querySelector(
      '.mr-radio[value="' + mrwidPage + '"]'
    );
    if (mrwidPageSelect) {
      mrwidPageSelect.value = mrwidPage;
    }
    if (mrwidRadios) {
      for (id = 0; id < mrwidRadios.length; id++) {
        mrwidRadios[id].removeAttribute("checked");
      }
    }
    if (mrwidCurrentRadio) {
      mrwidCurrentRadio.setAttribute("checked", "checked");
    }
    mrwidLayout.classList.remove("mr-transitionright", "mr-transitionleft");
    if (currentElement.classList.contains("mr-next")) {
      mrwidLayout.classList.add("mr-transitionright");
    } else if (currentElement.classList.contains("mr-prev")) {
      mrwidLayout.classList.add("mr-transitionleft");
    }
    if (mrwidLayout.querySelector(".mr-page" + mrwidPage + " .mr-noscript")) {
      mrwidLayout.querySelector(
        ".mr-page" + mrwidPage
      ).innerHTML = mrwidLayout.querySelector(
        ".mr-page" + mrwidPage + " .mr-noscript"
      ).textContent;
    }
    const mrwidPages = mrwidLayout.querySelectorAll(".mr-pages");
    const mrwidActivePages = mrwidLayout.querySelectorAll(".mr-pages.active");
    const mrwidNewPage = mrwidLayout.querySelector(".mr-page" + mrwidPage);
    for (id = 0; id < mrwidActivePages.length; id++) {
      const mrwidActivePage = mrwidActivePages[id];
      mrwidActivePage.classList.remove("active", "inactive", "open");
    }
    setTimeout(function () {
      for (id = 0; id < mrwidPages.length; id++) {
        const mrwidPage = mrwidPages[id];
        mrwidPage.classList.remove("active");
        mrwidPage.classList.add("inactive");
      }
      mrwidNewPage.classList.remove("inactive");
      mrwidNewPage.classList.add("active");
      if (
        mrwidLayout.querySelector(".mr-page" + (parseInt(mrwidPage) + 1)) &&
        mrwidLayout.querySelector(".mr-below")
      ) {
        mrwidLayout.querySelector(".mr-below").classList.remove("mr-hide");
      } else if (mrwidLayout.querySelector(".mr-below")) {
        mrwidLayout.querySelector(".mr-below").classList.add("mr-hide");
      }
      if (mrwidLayout.classList.contains("mr-autoscroll")) {
        rect = mrwidLayout.getBoundingClientRect();
        const elementoffset = rect.top + window.pageYOffset;
        mrScrollTo(elementoffset, 750);
      }
    }, 400);
  }
  setTimeout(function () {
    currentElement.classList.remove("loading");
    mrwidTabs();
  }, 500);
}
/*end*/
/*pagetoggles-0*/
function mrwidNext(currentElement) {
  const mrwidLayout = currentElement.closest(".mr-layout");
  let mrwidPage = mrwidLayout.querySelector(".mr-pageselect");
  if (mrwidPage) {
    mrwidPage = mrwidPage.value;
    const mrwidPageLastValue = mrwidLayout.querySelector(
      ".mr-pageselect option:last-child"
    ).value;
    if (mrwidPage < parseInt(mrwidPageLastValue)) {
      mrwidPage = parseInt(mrwidPage) + 1;
    } else {
      mrwidPage = 1;
    }
    mrwidChangePage(currentElement, mrwidLayout, mrwidPage);
  }
}
function mrwidPrev(currentElement) {
  const mrwidLayout = currentElement.closest(".mr-layout");
  let mrwidPage = mrwidLayout.querySelector(".mr-pageselect").value;
  const mrwidPageFirstValue = mrwidLayout.querySelector(
    ".mr-pageselect option:first-child"
  ).value;
  const mrwidPageLastValue = mrwidLayout.querySelector(
    ".mr-pageselect option:last-child"
  ).value;
  if (mrwidPage == parseInt(mrwidPageFirstValue)) {
    mrwidPage = parseInt(mrwidPageLastValue);
  } else {
    mrwidPage = mrwidPage - 1;
  }
  mrwidChangePage(currentElement, mrwidLayout, mrwidPage);
}
/*end*/
/*pagetoggles-3*/
function mrwidBelow(currentElement) {
  const mrwidLayout = currentElement.closest(".mr-layout");
  const mrwidPageSelect = mrwidLayout.querySelector(".mr-pageselect");
  const mrwidRadios = mrwidLayout.querySelectorAll(".mr-radio");
  let mrwidPage = mrwidPageSelect.options[mrwidPageSelect.selectedIndex].value;
  let mrwidNewPage = mrwidLayout.querySelector(
    ".mr-page" + (parseInt(mrwidPage) + 1)
  );
  mrwidLayout.classList.remove("mr-transitionright", "mr-transitionleft");
  if (!mrwidNewPage) {
    mrwidLayout.querySelector(".mr-page1").classList.add("active");
    currentElement.style.classList.remove("mr-hide");
  } else {
    if (mrwidNewPage.querySelector(".mr-noscript")) {
      mrwidNewPage.innerHTML = mrwidNewPage.querySelector(
        ".mr-noscript"
      ).textContent;
    }
    mrwidNewPage.classList.remove("inactive");
    mrwidNewPage.classList.add("active");
    if (mrwidPageSelect) {
      mrwidPageSelect.value = parseInt(mrwidPage) + 1;
    }
    if (mrwidRadios) {
      for (id = 0; id < mrwidRadios.length; id++) {
        mrwidRadios[id].removeAttribute("checked");
      }
    }
    let mrwidCurrentRadio = mrwidLayout.querySelector(
      '.mr-radio[value="' + (parseInt(mrwidPage) + 1) + '"]'
    );
    if (mrwidCurrentRadio) {
      mrwidCurrentRadio.setAttribute("checked", "checked");
    }
    if (
      !mrwidLayout.querySelector(".mr-page" + (parseInt(mrwidPage) + 2)) &&
      mrwidLayout.querySelector(".mr-below")
    ) {
      mrwidLayout.querySelector(".mr-below").classList.add("mr-hide");
    }
  }
  setTimeout(function () {
    currentElement.classList.remove("loading");
    mrwidTabs();
  }, 600);
}
/*end*/
function mrAutoPlay() {
  const mrwidsAutoPlay = document.querySelectorAll(
    ".mr-layout[class*=mr-autoplay]"
  );
  if (mrwidsAutoPlay) {
    for (id = 0; id < mrwidsAutoPlay.length; id++) {
      const currentElement = mrwidsAutoPlay[id];
      const mrwidAutoPlayClasses = [].slice
        .call(currentElement.classList)
        .toString();
      const mrwidAutoPlayClass = mrwidAutoPlayClasses.substring(
        mrwidAutoPlayClasses.lastIndexOf("mr-autoplay") + 11,
        mrwidAutoPlayClasses.lastIndexOf("s")
      );
      if (!currentElement.classList.contains("mr-hovering")) {
        mrwidNext(currentElement);
      }
      setTimeout(mrAutoPlay, parseInt(mrwidAutoPlayClass) * 1000);
    }
  }
}
function mrwidTabs() {
  /*tabs*/
  const mrwidItemsTabs = document.querySelectorAll(".mr-tabs .mr-tab");
  if (mrwidItemsTabs) {
    for (id = 0; id < mrwidItemsTabs.length; id++) {
      const mrwidItemTab = mrwidItemsTabs[id];
      const mrwidTabId = mrwidItemTab.className.split(" ")[0];
      const mrwidLayout = mrwidItemTab.closest(".mr-layout");
      const mrwidThis = mrwidLayout.querySelector(
        ".mr-pages.active .mr-item." + mrwidTabId
      );
      if (mrwidThis) {
        if (mrwidItemTab.classList.contains("mr-hide")) {
          mrwidItemTab.classList.remove("mr-hide");
        }
      } else {
        mrwidItemTab.classList.add("mr-hide");
      }
    }
  }
  /*end*/
}
function mrwidTabsChange(mrwidThis) {
  /*tabs*/
  const mrwidTabId = mrwidThis.className.split(" ")[0];
  const mrwidLayout = mrwidThis.closest(".mr-layout");
  const mrwidTabs = mrwidLayout.querySelectorAll(".mr-tab");
  const mrwidActiveTab = mrwidLayout.querySelector(".mr-tab." + mrwidTabId);
  const mrWids = mrwidLayout.querySelectorAll(".mr-pages.active .mr-item");
  const mrWidsInactive = mrwidLayout.querySelectorAll(
    ".mr-pages.active .mr-item.inactive"
  );
  if (mrwidThis.classList.contains("active")) {
    if (mrwidTabs) {
      for (id = 0; id < mrwidTabs.length; id++) {
        const mrwidTab = mrwidTabs[id];
        mrwidTab.classList.remove("active", "inactive");
      }
    }
    if (mrWids) {
      for (id = 0; id < mrWids.length; id++) {
        const mrWid = mrWids[id];
        mrWid.classList.remove("active", "inactive");
      }
    }
  } else {
    if (mrwidTabs && !mrwidLayout.classList.contains("mr-keepactive")) {
      for (id = 0; id < mrwidTabs.length; id++) {
        const mrwidTab = mrwidTabs[id];
        mrwidTab.classList.remove("active");
        mrwidTab.classList.add("inactive");
      }
    }
    if (mrWids) {
      for (id = 0; id < mrWids.length; id++) {
        const mrWid = mrWids[id];
        if (mrWid.classList.contains(mrwidTabId)) {
          mrWid.classList.remove("inactive");
          mrWid.classList.add("active");
          if (mrwidActiveTab) {
            mrwidActiveTab.classList.remove("inactive");
            mrwidActiveTab.classList.add("active");
          }
        } else {
          if (
            !mrWidsInactive.length ||
            !mrwidLayout.classList.contains("mr-keepactive")
          ) {
            mrWid.classList.remove("active");
            mrWid.classList.add("inactive");
          }
        }
      }
    }
  }
  const mrwidCheckState = mrwidLayout.querySelectorAll(".mr-tab.active");
  if (!mrwidCheckState.length) {
    if (mrwidTabs) {
      for (id = 0; id < mrwidTabs.length; id++) {
        const mrwidTab = mrwidTabs[id];
        mrwidTab.classList.remove("active", "inactive");
      }
    }
    if (mrWids) {
      for (id = 0; id < mrWids.length; id++) {
        const mrWid = mrWids[id];
        mrWid.classList.remove("active", "inactive");
      }
    }
  }
  /*end*/
}
document.addEventListener("DOMContentLoaded", function () {
  /*pagetoggles-1*/
  const mrSelects = document.querySelectorAll(".mr-pagination .mr-pageselect");
  if (mrSelects) {
    for (id = 0; id < mrSelects.length; id++) {
      mrSelects[id].selectedIndex = "0";
    }
  }
  /*end*/
  /*itemoptions-remember*/
  const mrwids = document.querySelectorAll(
    ".mr-remember > *:not(.mr-pagination):not(.mr-tabs).active > *"
  );
  if (mrwids) {
    if (mrGetCookie("mrRemember") != "") {
      const mrRemembered = mrGetCookie("mrRemember");
      for (id = 0; id < mrwids.length; id++) {
        console.log(mrwids.length);
        if (
          !mrwids[id].classList.contains(mrRemembered) ||
          (mrwids[id].classList.contains(mrRemembered) &&
            mrwids.length == 1 &&
            mrwids[id].classList.contains("active"))
        ) {
          mrwids[id].classList.remove("active");
          mrwids[id].classList.add("inactive");
        } else {
          mrwids[id].classList.remove("inactive");
          mrwids[id].classList.add("active");
        }
      }
    } else {
      for (id = 0; id < mrwids.length; id++) {
        if (mrwids[id].classList.contains("active")) {
          mrCreateCookie(mrwids[id]);
        }
      }
    }
  }
  /*end*/
  /*itemoptions-url*/
  window.addEventListener("popstate", function () {
    const checkattr = document.querySelectorAll(".mr-url > *:not(.mr-pagination):not(.mr-tabs) > *[url]");
    if (checkattr) {
      for (id = 0; id < checkattr.length; id++) {
        const getWidUrl = checkattr[id].getAttribute("url");
        if (getWidUrl.indexOf("/./") > -1) {
          const getWidUrl = getWidUrl.replace("./", "");
        }
        if (window.location.href.indexOf(getWidUrl) > -1) {
          checkattr[id].classList.remove("inactive");
          checkattr[id].classList.add("active");
        } else {
          checkattr[id].classList.remove("active");
          checkattr[id].classList.add("inactive");
        }
      }
    }
  });
  /*end*/
  /*itemoptions-windowheight*/
  const mrwidsHeightFix = document.querySelectorAll(".mr-windowheight");
  if (mrwidsHeightFix) {
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty("--vh", vh + "px");
    window.addEventListener("resize", function () {
      const vh = window.innerHeight * 0.01;
      document.documentElement.style.setProperty("--vh", vh + "px");
    });
  }
  /*end*/
  /*autoplay*/
  const mrwidAutoPlay = document.querySelector(
    ".mr-layout[class*=mr-autoplay]"
  );
  if (mrwidAutoPlay) {
    const mrwidAutoPlayClasses = [].slice
      .call(mrwidAutoPlay.classList)
      .toString();
    const mrwidAutoPlayClass = mrwidAutoPlayClasses.substring(
      mrwidAutoPlayClasses.lastIndexOf("mr-autoplay") + 14,
      mrwidAutoPlayClasses.lastIndexOf("s")
    );
    setTimeout(mrAutoPlay, parseInt(mrwidAutoPlayClass) * 1000);
  }
  /*end*/
  const mrwidsHover = document.querySelectorAll(
    ".mr-layout.mr-hover,.mr-layout[class*=mr-autoplay]"
  );
  if (mrwidsHover) {
    for (id = 0; id < mrwidsHover.length; id++) {
      mrwidsHover[id].addEventListener("mouseover", function (event) {
        /*itemoptions-hover*/
        if (
          event.target.matches(
            ".mr-layout.mr-hover > *:not(.mr-pagination):not(.mr-tabs).active > *"
          )
        ) {
          mrwidMain(event.target);
          event.stopPropagation();
        } else if (
          event.target.matches(".mr-layout.mr-hover > .mr-tabs > .mr-tab")
        ) {
          const mrwidThis = event.target;
          mrwidTabsChange(mrwidThis);
          event.stopPropagation();
        }
        /*end*/
        if (
          event.target.matches(".mr-layout[class*=mr-autoplay] > *:not(.mr-pagination):not(.mr-tabs) > *") ||
          event.target.matches(".mr-layout[class*=mr-autoplay] .mr-tab") ||
          event.target.matches(
            ".mr-layout[class*=mr-autoplay] .mr-pagination"
          ) ||
          event.target.matches(".mr-layout[class*=mr-autoplay] .mr-arrows") ||
          event.target.matches(".mr-layout[class*=mr-autoplay] .mr-below")
        ) {
          event.target.closest(".mr-layout").classList.add("mr-hovering");
        }
      });
      mrwidsHover[id].addEventListener("mouseleave", function (event) {
        if (event.target.classList.contains("mr-hovering")) {
          event.target.classList.remove("mr-hovering");
        }
        /*itemoptions-hover*/
        const mrwid = event.target.querySelectorAll(
          ".mr-layout.mr-hover > *:not(.mr-pagination):not(.mr-tabs).active > *"
        );
        if (mrwid) {
          for (id = 0; id < mrwid.length; id++) {
            mrwid[id].classList.remove("active", "inactive");
          }
          if (event.target.classList.contains("mr-subitemactive")) {
            const mrwidSubcats = event.target.querySelectorAll(
              ".mr-pages.active > .mr-subitem"
            );
            if (mrwidSubcats) {
              for (id = 0; id < mrwidSubcats.length; id++) {
                mrwidSubcats[id].classList.add("mr-hide");
              }
            }
            event.stopPropagation();
          }
        }
        /*end*/
      });
    }
  }
  mrwidTabs();
});
document.addEventListener("click", function (event) {
  if (
    event.target.matches(
      '.mr-layout:not(.mr-hover) .mr-pages a[href]:not([href="#"]):not([href="javascript:void(0)"]):not([target="_blank"])'
    )
  ) {
    const mrwidNotThis = event.target
      .closest(".mr-layout")
      .querySelectorAll(".mr-item");
    const mrwidThisTabs = event.target
      .closest(".mr-layout")
      .querySelectorAll(".mr-tab");
    const mrwidThisID = event.target
      .closest(".mr-item")
      .className.split(" ")[0];
    if (mrwidNotThis) {
      for (id = 0; id < mrwidNotThis.length; id++) {
        mrwidNotThis[id].classList.add("close");
      }
    }
    if (mrwidThisTabs) {
      for (id = 0; id < mrwidThisTabs.length; id++) {
        if (mrwidThisTabs[id].classList.contains(mrwidThisID)) {
          mrwidThisTabs[id].classList.remove("close");
          mrwidThisTabs[id].classList.add("open");
        } else {
          mrwidThisTabs[id].classList.add("close");
        }
      }
    }
    const mrwidThis = event.target.closest(".mr-item");
    mrwidThis.classList.remove("close");
    mrwidThis.classList.add("open");
    event.stopPropagation();
  } else if (
    event.target.matches(".mr-layout:not(.mr-hover) > *:not(.mr-pagination):not(.mr-tabs) > *") ||
    event.target.matches(".mr-layout:not(.mr-hover) .mr-item-container") ||
    event.target.matches(".mr-layout:not(.mr-hover) .mr-image") ||
    event.target.matches(".mr-layout:not(.mr-hover) .mr-title") ||
    event.target.matches(
      ".mr-layout:not(.mr-hover) .mr-item:not(.active) .mr-content, .mr-layout:not(.mr-hover) .mr-item:not(.active) .mr-content *"
    )
  ) {
    if(event.target.matches(".mrdev-block > .mr-layout:not(.mr-hover) > *:not(.mr-pagination):not(.mr-tabs) > *:not(.mr-item)")) {
      const mrDevBlockItems = event.target.parentNode.children;
      for (id = 0; id < mrDevBlockItems.length; id++) {
        mrDevBlockItems[id].classList.add("mr-item");
      }
    }
    const mrwidThis = event.target;
    mrwidMain(mrwidThis);
    event.stopPropagation();
  } /*tabs*/ else if (
    event.target.matches(".mr-tabs .mr-tab") &&
    !event.target.matches(".mr-layout.mr-donotinactive .mr-tabs .mr-tab.active")
  ) {
    const mrwidThis = event.target;
    mrwidTabsChange(mrwidThis);
    event.stopPropagation();
  } /*end*/ /*pagetoggles-0*/ else if (event.target.matches(".mr-next")) {
    const currentElement = event.target;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidNext(currentElement);
    }
    event.stopPropagation();
  } else if (event.target.matches(".mr-next span")) {
    const currentElement = event.target.parentElement;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidNext(currentElement);
    }
    event.stopPropagation();
  } else if (event.target.matches(".mr-prev")) {
    const currentElement = event.target;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidPrev(currentElement);
    }
    event.stopPropagation();
  } else if (event.target.matches(".mr-prev span")) {
    const currentElement = event.target.parentElement;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidPrev(currentElement);
    }
    event.stopPropagation();
  } /*end*/ /*pagetoggles-2*/ else if (
    event.target.matches(".mr-radio:not([checked])")
  ) {
    const currentElement = event.target;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      const mrwidLayout = currentElement.closest(".mr-layout");
      const mrwidPage = currentElement.value;
      mrwidChangePage(currentElement, mrwidLayout, mrwidPage);
    }
    event.stopPropagation();
  } /*end*/ /*pagetoggles-1*/ else if (event.target.matches(".mr-pageselect")) {
    event.target.addEventListener("change", function (event) {
      const currentElement = event.target;
      if (!currentElement.classList.contains("loading")) {
        currentElement.classList.add("loading");
        const mrwidLayout = currentElement.closest(".mr-layout");
        const mrwidPage = currentElement.value;
        mrwidChangePage(currentElement, mrwidLayout, mrwidPage);
      }
    });
    event.stopPropagation();
  } /*end*/ /*pagetoggles-3*/ else if (event.target.matches(".mr-below")) {
    const currentElement = event.target;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidBelow(currentElement);
    }
    event.stopPropagation();
  } else if (event.target.matches(".mr-below span")) {
    const currentElement = event.target.parentElement;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidBelow(currentElement);
    }
    event.stopPropagation();
  } /*end*/ /*pagetoggles-4*/ else if (
    event.target.matches("button.mr-scroll")
  ) {
    const currentElement = event.target;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidBelow(currentElement);
    }
    event.stopPropagation();
  } else if (event.target.matches("button.mr-scroll span")) {
    const currentElement = event.target.parentElement;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidBelow(currentElement);
    }
    event.stopPropagation();
  } /*end*/
});
/*pagetoggles-5*/
document.addEventListener("keydown", function (event) {
  const mrwidKeyboard = document.querySelectorAll(".mr-keyboard");
  if (mrwidKeyboard) {
    for (id = 0; id < mrwidKeyboard.length; id++) {
      const currentElement = mrwidKeyboard[id];
      if (mrIsInView(currentElement)) {
        if (!currentElement.classList.contains("loading")) {
          currentElement.classList.add("loading");
          const mrwidLayout = currentElement.parentElement;
          if (event.keyCode === 39) {
            mrwidNext(currentElement);
            return false;
          } else if (event.keyCode === 37) {
            mrwidPrev(currentElement);
            return false;
          } else if (
            event.keyCode === 40 &&
            currentElement.classList.contains("mr-below")
          ) {
            mrwidBelow(currentElement);
            return false;
          } else if (event.keyCode === 49 || event.keyCode === 97) {
            const mrwidPage = 1;
            mrwidChangePage(currentElement, mrwidLayout, mrwidPage);
            return false;
          } else if (event.keyCode === 50 || event.keyCode === 98) {
            const mrwidPage = 2;
            mrwidChangePage(currentElement, mrwidLayout, mrwidPage);
            return false;
          } else if (event.keyCode === 51 || event.keyCode === 99) {
            const mrwidPage = 3;
            mrwidChangePage(currentElement, mrwidLayout, mrwidPage);
            return false;
          } else if (event.keyCode === 52 || event.keyCode === 100) {
            const mrwidPage = 4;
            mrwidChangePage(currentElement, mrwidLayout, mrwidPage);
            return false;
          } else if (event.keyCode === 53 || event.keyCode === 101) {
            const mrwidPage = 5;
            mrwidChangePage(currentElement, mrwidLayout, mrwidPage);
            return false;
          } else if (event.keyCode === 54 || event.keyCode === 102) {
            const mrwidPage = 6;
            mrwidChangePage(currentElement, mrwidLayout, mrwidPage);
            return false;
          } else if (event.keyCode === 55 || event.keyCode === 103) {
            const mrwidPage = 7;
            mrwidChangePage(currentElement, mrwidLayout, mrwidPage);
            return false;
          } else if (event.keyCode === 56 || event.keyCode === 104) {
            const mrwidPage = 8;
            mrwidChangePage(currentElement, mrwidLayout, mrwidPage);
            return false;
          } else if (event.keyCode === 57 || event.keyCode === 105) {
            const mrwidPage = 9;
            mrwidChangePage(currentElement, mrwidLayout, mrwidPage);
            return false;
          }
        }
      }
    }
  }
});
/*end*/
let mrScrollTimer;
let mrInitst;
window.addEventListener("scroll", function (event) {
  /*pagetoggles-4*/
  const mrwidsScroll = document.querySelectorAll("button.mr-scroll");
  if (mrwidsScroll) {
    clearTimeout(mrScrollTimer);
    mrScrollTimer = setTimeout(function () {
      for (id = 0; id < mrwidsScroll.length; id++) {
        const currentElement = mrwidsScroll[id];
        if (mrIsInView(currentElement)) {
          mrwidBelow(currentElement);
        }
      }
    }, 400);
  }
  /*end*/
  /*imagestype-parallax*/
  mrParallax(
    ".mr-theme .mr-layout.mr-parallax.mr-background .mr-pages.active .mr-image"
  );
  const st = window.pageYOffset;
  mrInitst = st;
  const mrthumbparElements = document.querySelectorAll(
    ".mr-theme .mr-layout.mr-parallax.mr-thumbnail:not(.mr-background) .mr-pages.active .mr-image img"
  );
  if (mrthumbparElements) {
    for (id = 0; id < mrthumbparElements.length; id++) {
      mrthumbparElements[id].style.transform =
        "translateY(-" + st * 0.04 + "px)";
    }
  }
  /*end*/
});
