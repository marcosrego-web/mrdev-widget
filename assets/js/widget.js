function mrwidMain(mrwidThis) {
  if (!mrwidThis.matches(".mr-widget-item")) {
    mrwidThis = event.target.closest(".mr-widget-item");
  }
  const mrwidLayout = mrwidThis.closest(".mr-layout");
  const mrwidPage = mrwidThis.closest(".mr-widget-pages");
  /*tabs*/
  const mrwidItemsTabs = mrwidLayout.querySelector(
    ".mr-widget-tabs.mr-widget-items"
  );
  const mrwidTabs = mrwidLayout.querySelector(
    ".mr-widget-tabs:not(.mr-widget-items)"
  );
  if (mrwidTabs && !mrwidLayout.classList.contains("mr-donotinactive")) {
    const mrtabs = mrwidTabs.querySelectorAll(".mr-widget-tab");
    for (id = 0; id < mrtabs.length; id++) {
      const mrtab = mrtabs[id];
      mrtab.classList.remove("mr-active", "mr-inactive", "open");
    }
  }
  /*end*/
  if (mrwidThis.classList.contains("mr-active")) {
    mrCloseActive(
      mrwidThis,
      mrwidLayout,
      mrwidPage /*tabs*/,
      mrwidItemsTabs /*end*/
    );
  } else if (!mrwidThis.classList.contains("mr-active")) {
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
  const mrwids = mrwidPage.querySelectorAll(".mr-widget-item");
  for (id = 0; id < mrwids.length; id++) {
    /*globallayoutoptions-keepactive*/
    if (mrwidLayout.classList.contains("mr-keepactive")) {
      if (!mrwids[id].classList.contains("mr-active")) {
        mrwids[id].classList.add("mr-inactive");
      }
    } /*end*/ else if (!mrwidLayout.classList.contains("mr-keepactive")) {
      mrwids[id].classList.remove("mr-active", "open", "mr-widget-scroll");
      mrwids[id].classList.add("mr-inactive");
    }
    /*tabs*/
    if (mrwidItemsTabs) {
      if (mrwidLayout.classList.contains("mr-keepactive")) {
        if (!mrwids[id].classList.contains("mr-active")) {
          const mrwidTabId = mrwids[id].className.split(" ")[0];
          const mrwidTab = mrwidItemsTabs.querySelector(
            ".mr-widget-tab." + mrwidTabId
          );
          if (mrwidTab) {
            mrwidTab.classList.add("mr-inactive");
          }
        }
      } else if (!mrwidLayout.classList.contains("mr-keepactive")) {
        const mrwidTabId = mrwids[id].className.split(" ")[0];
        const mrwidTab = mrwidItemsTabs.querySelector(
          ".mr-widget-tab." + mrwidTabId
        );
        if (mrwidTab) {
          mrwidTab.classList.remove("mr-active", "open", "mr-widget-scroll");
          mrwidTab.classList.add("mr-inactive");
        }
      }
    }
    /*end*/
  }
  mrwidThis.classList.remove("mr-inactive");
  mrwidThis.classList.add("mr-active");
  /*tabs*/
  if (mrwidItemsTabs) {
    const mrwidTabId = mrwidThis.className.split(" ")[0];
    const mrwidTab = mrwidItemsTabs.querySelector(
      ".mr-widget-tab." + mrwidTabId
    );
    if (mrwidTab) {
      mrwidTab.classList.remove("mr-inactive");
      mrwidTab.classList.add("mr-active");
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
          mrwidSubCats[id].classList.remove("mr-inactive");
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
    mrwidThis.classList.remove("mr-active", "open");
    mrwidThis.classList.add("mr-inactive");
    /*tabs*/
    if (mrwidItemsTabs) {
      const mrwidTabId = mrwidThis.className.split(" ")[0];
      const mrwidTab = mrwidItemsTabs.querySelector(
        ".mr-widget-tab." + mrwidTabId
      );
      if (mrwidTab) {
        mrwidTab.classList.remove("mr-active", "mr-inactive", "open");
        mrwidTab.classList.add("mr-inactive");
      }
    }
    /*end*/
  }
  if (
    mrwidLayout.classList.contains("mr-subitemactive") &&
    mrwidLayout.classList.contains("mr-hideinactives")
  ) {
    const mrwids = mrwidPage.querySelectorAll(".mr-widget-item");
    if (mrwids) {
      for (id = 0; id < mrwids.length; id++) {
        if (mrwids[id].classList.contains("mr-subitem")) {
          mrwids[id].classList.add("mr-hide");
        }
        mrwids[id].classList.remove("mr-active", "mr-inactive", "open");
        /*tabs*/
        if (mrwidItemsTabs) {
          const mrwidTabId = mrwids[id].className.split(" ")[0];
          const mrwidTab = mrwidItemsTabs.querySelector(
            ".mr-widget-tab." + mrwidTabId
          );
          if (mrwidTab) {
            mrwidTab.classList.remove("mr-active", "mr-inactive", "open");
          }
        }
        /*end*/
      }
    }
  }
}
function mrConfirmStatus(mrwidPage /*tabs*/, mrwidItemsTabs /*end*/) {
  const mrwidCheckState = mrwidPage.querySelectorAll(".mr-active");
  if (!mrwidCheckState.length) {
    const mrwids = mrwidPage.querySelectorAll(".mr-widget-item");
    for (id = 0; id < mrwids.length; id++) {
      mrwids[id].classList.remove("mr-inactive");
    }
  }
  /*tabs*/
  if (mrwidItemsTabs) {
    const mrwidCheckState = mrwidItemsTabs.querySelectorAll(
      ".mr-widget-tab.mr-active"
    );
    if (!mrwidCheckState.length) {
      const mrtabs = mrwidItemsTabs.querySelectorAll(".mr-widget-tab");
      if (mrtabs) {
        for (id = 0; id < mrtabs.length; id++) {
          mrtabs[id].classList.remove("mr-inactive");
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
      '.mr-widget-pageselect option[value="' + mrwidPage + '"]'
    )
  ) {
    const mrwidPageSelect = mrwidLayout.querySelector(".mr-widget-pageselect");
    const mrwidRadios = mrwidLayout.querySelectorAll(".mr-widget-radio");
    const mrwidCurrentRadio = mrwidLayout.querySelector(
      '.mr-widget-radio[value="' + mrwidPage + '"]'
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
    if (
      mrwidLayout.querySelector(".mr-widget-page" + mrwidPage + " .mr-noscript")
    ) {
      mrwidLayout.querySelector(".mr-widget-page" + mrwidPage).innerHTML =
        mrwidLayout.querySelector(
          ".mr-widget-page" + mrwidPage + " .mr-noscript"
        ).textContent;
    }
    const mrwidPages = mrwidLayout.querySelectorAll(".mr-widget-pages");
    const mrwidActivePages = mrwidLayout.querySelectorAll(
      ".mr-widget-pages.mr-active"
    );
    const mrwidNewPage = mrwidLayout.querySelector(
      ".mr-widget-page" + mrwidPage
    );
    for (id = 0; id < mrwidActivePages.length; id++) {
      const mrwidActivePage = mrwidActivePages[id];

      mrwidActivePage.classList.remove("mr-active", "mr-inactive", "open");
      mrwidActivePage.classList.remove(
        "mr-transitionright",
        "mr-transitionleft"
      );
      if (currentElement.classList.contains("mr-widget-next")) {
        mrwidActivePage.classList.add("mr-transitionleft");
      } else if (currentElement.classList.contains("mr-widget-prev")) {
        mrwidActivePage.classList.add("mr-transitionright");
      }
    }
    setTimeout(function () {
      for (id = 0; id < mrwidPages.length; id++) {
        const mrwidPage = mrwidPages[id];
        mrwidPage.classList.remove("mr-active");
        mrwidPage.classList.add("mr-inactive");
      }
      mrwidNewPage.classList.remove(
        "mr-inactive",
        "mr-transitionright",
        "mr-transitionleft"
      );
      if (currentElement.classList.contains("mr-widget-next")) {
        mrwidNewPage.classList.add("mr-transitionright");
      } else if (currentElement.classList.contains("mr-widget-prev")) {
        mrwidNewPage.classList.add("mr-transitionleft");
      }
      mrwidNewPage.classList.add("mr-active");
      if (
        mrwidLayout.querySelector(
          ".mr-widget-page" + (parseInt(mrwidPage) + 1)
        ) &&
        mrwidLayout.querySelector(".mr-widget-below")
      ) {
        mrwidLayout
          .querySelector(".mr-widget-below")
          .classList.remove("mr-hide");
      } else if (mrwidLayout.querySelector(".mr-widget-below")) {
        mrwidLayout.querySelector(".mr-widget-below").classList.add("mr-hide");
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
  let mrwidPage = mrwidLayout.querySelector(".mr-widget-pageselect");
  if (mrwidPage) {
    mrwidPage = mrwidPage.value;
    const mrwidPageLastValue = mrwidLayout.querySelector(
      ".mr-widget-pageselect option:last-child"
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
  let mrwidPage = mrwidLayout.querySelector(".mr-widget-pageselect").value;
  const mrwidPageFirstValue = mrwidLayout.querySelector(
    ".mr-widget-pageselect option:first-child"
  ).value;
  const mrwidPageLastValue = mrwidLayout.querySelector(
    ".mr-widget-pageselect option:last-child"
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
  const mrwidPageSelect = mrwidLayout.querySelector(".mr-widget-pageselect");
  const mrwidRadios = mrwidLayout.querySelectorAll(".mr-widget-radio");
  let mrwidPage = mrwidPageSelect.options[mrwidPageSelect.selectedIndex].value;
  let mrwidNewPage = mrwidLayout.querySelector(
    ".mr-widget-page" + (parseInt(mrwidPage) + 1)
  );
  if (!mrwidNewPage) {
    mrwidLayout.querySelector(".mr-widget-page1").classList.add("mr-active");
    currentElement.style.classList.remove("mr-hide");
  } else {
    if (mrwidNewPage.querySelector(".mr-noscript")) {
      mrwidNewPage.innerHTML =
        mrwidNewPage.querySelector(".mr-noscript").textContent;
    }
    mrwidNewPage.classList.remove(
      "mr-inactive",
      "mr-transitionright",
      "mr-transitionleft"
    );
    mrwidNewPage.classList.add("mr-active");
    if (mrwidPageSelect) {
      mrwidPageSelect.value = parseInt(mrwidPage) + 1;
    }
    if (mrwidRadios) {
      for (id = 0; id < mrwidRadios.length; id++) {
        mrwidRadios[id].removeAttribute("checked");
      }
    }
    let mrwidCurrentRadio = mrwidLayout.querySelector(
      '.mr-widget-radio[value="' + (parseInt(mrwidPage) + 1) + '"]'
    );
    if (mrwidCurrentRadio) {
      mrwidCurrentRadio.setAttribute("checked", "checked");
    }
    if (
      !mrwidLayout.querySelector(
        ".mr-widget-page" + (parseInt(mrwidPage) + 2)
      ) &&
      mrwidLayout.querySelector(".mr-widget-below")
    ) {
      mrwidLayout.querySelector(".mr-widget-below").classList.add("mr-hide");
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
    ".mr-layout[class*=mr-widget-autoplay]"
  );
  if (mrwidsAutoPlay) {
    for (id = 0; id < mrwidsAutoPlay.length; id++) {
      const currentElement = mrwidsAutoPlay[id];
      const mrwidAutoPlayClasses = [].slice
        .call(currentElement.classList)
        .toString();
      const mrwidAutoPlayClass = mrwidAutoPlayClasses.substring(
        mrwidAutoPlayClasses.lastIndexOf("mr-widget-autoplay") + 18,
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
  const mrwidItemsTabs = document.querySelectorAll(
    ".mr-widget-tabs .mr-widget-tab"
  );
  if (mrwidItemsTabs) {
    for (id = 0; id < mrwidItemsTabs.length; id++) {
      const mrwidItemTab = mrwidItemsTabs[id];
      const mrwidTabId = mrwidItemTab.className.split(" ")[0];
      const mrwidLayout = mrwidItemTab.closest(".mr-layout");
      const mrwidThis = mrwidLayout.querySelector(
        ".mr-widget-pages.mr-active .mr-widget-item." + mrwidTabId
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
  const mrwidTabs = mrwidLayout.querySelectorAll(".mr-widget-tab");
  const mrwidActiveTab = mrwidLayout.querySelector(
    ".mr-widget-tab." + mrwidTabId
  );
  const mrWids = mrwidLayout.querySelectorAll(
    ".mr-widget-pages.mr-active .mr-widget-item"
  );
  const mrWidsInactive = mrwidLayout.querySelectorAll(
    ".mr-widget-pages.mr-active .mr-widget-item.mr-inactive"
  );
  if (mrwidThis.classList.contains("active")) {
    if (mrwidTabs) {
      for (id = 0; id < mrwidTabs.length; id++) {
        const mrwidTab = mrwidTabs[id];
        mrwidTab.classList.remove("mr-active", "mr-inactive");
      }
    }
    if (mrWids) {
      for (id = 0; id < mrWids.length; id++) {
        const mrWid = mrWids[id];
        mrWid.classList.remove("mr-active", "mr-inactive");
      }
    }
  } else {
    if (mrwidTabs && !mrwidLayout.classList.contains("mr-keepactive")) {
      for (id = 0; id < mrwidTabs.length; id++) {
        const mrwidTab = mrwidTabs[id];
        mrwidTab.classList.remove("mr-active");
        mrwidTab.classList.add("mr-inactive");
      }
    }
    if (mrWids) {
      for (id = 0; id < mrWids.length; id++) {
        const mrWid = mrWids[id];
        if (mrWid.classList.contains(mrwidTabId)) {
          mrWid.classList.remove("mr-inactive");
          mrWid.classList.add("mr-active");
          if (mrwidActiveTab) {
            mrwidActiveTab.classList.remove("mr-inactive");
            mrwidActiveTab.classList.add("mr-active");
          }
        } else {
          if (
            !mrWidsInactive.length ||
            !mrwidLayout.classList.contains("mr-keepactive")
          ) {
            mrWid.classList.remove("mr-active");
            mrWid.classList.add("mr-inactive");
          }
        }
      }
    }
  }
  const mrwidCheckState = mrwidLayout.querySelectorAll(
    ".mr-widget-tab.mr-active"
  );
  if (!mrwidCheckState.length) {
    if (mrwidTabs) {
      for (id = 0; id < mrwidTabs.length; id++) {
        const mrwidTab = mrwidTabs[id];
        mrwidTab.classList.remove("mr-active", "mr-inactive");
      }
    }
    if (mrWids) {
      for (id = 0; id < mrWids.length; id++) {
        const mrWid = mrWids[id];
        mrWid.classList.remove("mr-active", "mr-inactive");
      }
    }
  }
  /*end*/
}
document.addEventListener("DOMContentLoaded", function () {
  /*pagetoggles-1*/
  const mrSelects = document.querySelectorAll(
    ".mr-pagination .mr-widget-pageselect"
  );
  if (mrSelects) {
    for (id = 0; id < mrSelects.length; id++) {
      mrSelects[id].selectedIndex = "0";
    }
  }
  /*end*/
  /*itemoptions-remember*/
  const mrwids = document.querySelectorAll(
    ".mr-remember .mr-widget-pages.mr-active .mr-widget-item"
  );
  if (mrwids) {
    if (mrGetCookie("mrRemember") != "") {
      const mrRemembered = mrGetCookie("mrRemember");
      for (id = 0; id < mrwids.length; id++) {
        if (
          !mrwids[id].classList.contains(mrRemembered) ||
          (mrwids[id].classList.contains(mrRemembered) &&
            mrwids.length == 1 &&
            mrwids[id].classList.contains("mr-active"))
        ) {
          mrwids[id].classList.remove("mr-active");
          mrwids[id].classList.add("mr-inactive");
        } else {
          mrwids[id].classList.remove("mr-inactive");
          mrwids[id].classList.add("mr-active");
        }
      }
    } else {
      for (id = 0; id < mrwids.length; id++) {
        if (mrwids[id].classList.contains("mr-active")) {
          mrCreateCookie(mrwids[id]);
        }
      }
    }
  }
  /*end*/
  /*itemoptions-url*/
  window.addEventListener("popstate", function () {
    const checkattr = document.querySelectorAll(".mr-url .mr-widget-item[url]");
    if (checkattr) {
      for (id = 0; id < checkattr.length; id++) {
        const getWidUrl = checkattr[id].getAttribute("url");
        if (getWidUrl.indexOf("/./") > -1) {
          const getWidUrl = getWidUrl.replace("./", "");
        }
        if (window.location.href.indexOf(getWidUrl) > -1) {
          checkattr[id].classList.remove("mr-inactive");
          checkattr[id].classList.add("mr-active");
        } else {
          checkattr[id].classList.remove("mr-active");
          checkattr[id].classList.add("mr-inactive");
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
    ".mr-layout[class*=mr-widget-autoplay]"
  );
  if (mrwidAutoPlay) {
    const mrwidAutoPlayClasses = [].slice
      .call(mrwidAutoPlay.classList)
      .toString();
    const mrwidAutoPlayClass = mrwidAutoPlayClasses.substring(
      mrwidAutoPlayClasses.lastIndexOf("mr-widget-autoplay") + 21,
      mrwidAutoPlayClasses.lastIndexOf("s")
    );
    setTimeout(mrAutoPlay, parseInt(mrwidAutoPlayClass) * 1000);
  }
  /*end*/
  const mrwidsHover = document.querySelectorAll(
    ".mr-layout.mr-hover,.mr-layout[class*=mr-widget-autoplay]"
  );
  if (mrwidsHover) {
    for (id = 0; id < mrwidsHover.length; id++) {
      mrwidsHover[id].addEventListener("mouseover", function (event) {
        /*itemoptions-hover*/
        if (
          event.target.matches(
            ".mr-layout.mr-hover > .mr-widget-pages.mr-active > .mr-widget-item"
          )
        ) {
          mrwidMain(event.target);
          event.stopPropagation();
        } else if (
          event.target.matches(
            ".mr-layout.mr-hover > .mr-widget-tabs > .mr-widget-tab"
          )
        ) {
          const mrwidThis = event.target;
          mrwidTabsChange(mrwidThis);
          event.stopPropagation();
        }
        /*end*/
        if (
          event.target.matches(
            ".mr-layout[class*=mr-widget-autoplay] .mr-widget-item"
          ) ||
          event.target.matches(
            ".mr-layout[class*=mr-widget-autoplay] .mr-widget-tab"
          ) ||
          event.target.matches(
            ".mr-layout[class*=mr-widget-autoplay] .mr-pagination"
          ) ||
          event.target.matches(
            ".mr-layout[class*=mr-widget-autoplay] .mr-widget-arrows"
          ) ||
          event.target.matches(
            ".mr-layout[class*=mr-widget-autoplay] .mr-widget-below"
          )
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
          ".mr-layout.mr-hover > .mr-widget-pages.mr-active > .mr-widget-item"
        );
        if (mrwid) {
          for (id = 0; id < mrwid.length; id++) {
            mrwid[id].classList.remove("mr-active", "mr-inactive");
          }
          if (event.target.classList.contains("mr-subitemactive")) {
            const mrwidSubcats = event.target.querySelectorAll(
              ".mr-widget-pages.mr-active > .mr-subitem"
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
      '.mr-layout:not(.mr-hover) .mr-widget-pages a[href]:not([href="#"]):not([href="javascript:void(0)"]):not([target="_blank"])'
    )
  ) {
    const mrwidNotThis = event.target
      .closest(".mr-layout")
      .querySelectorAll(".mr-widget-item");
    const mrwidThisTabs = event.target
      .closest(".mr-layout")
      .querySelectorAll(".mr-widget-tab");
    const mrwidThisID = event.target
      .closest(".mr-widget-item")
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
    const mrwidThis = event.target.closest(".mr-widget-item");
    mrwidThis.classList.remove("close");
    mrwidThis.classList.add("open");
    event.stopPropagation();
  } else if (
    event.target.matches(".mr-layout:not(.mr-hover) .mr-widget-item") ||
    event.target.matches(
      ".mr-layout:not(.mr-hover) .mr-widget-item-container"
    ) ||
    event.target.matches(".mr-layout:not(.mr-hover) .mr-image") ||
    event.target.matches(".mr-layout:not(.mr-hover) .mr-title") ||
    event.target.matches(
      ".mr-layout:not(.mr-hover) .mr-widget-item:not(.mr-active) .mr-content, .mr-layout:not(.mr-hover) .mr-widget-item:not(.mr-active) .mr-content *"
    )
  ) {
    const mrwidThis = event.target;
    mrwidMain(mrwidThis);
    event.stopPropagation();
  } /*tabs*/ else if (
    event.target.matches(".mr-widget-tabs .mr-widget-tab") &&
    !event.target.matches(
      ".mr-layout.mr-donotinactive .mr-widget-tabs .mr-widget-tab.mr-active"
    )
  ) {
    const mrwidThis = event.target;
    mrwidTabsChange(mrwidThis);
    event.stopPropagation();
  } /*end*/ /*pagetoggles-0*/ else if (
    event.target.matches(".mr-widget-next")
  ) {
    const currentElement = event.target;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidNext(currentElement);
    }
    event.stopPropagation();
  } else if (event.target.matches(".mr-widget-next span")) {
    const currentElement = event.target.parentElement;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidNext(currentElement);
    }
    event.stopPropagation();
  } else if (event.target.matches(".mr-widget-prev")) {
    const currentElement = event.target;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidPrev(currentElement);
    }
    event.stopPropagation();
  } else if (event.target.matches(".mr-widget-prev span")) {
    const currentElement = event.target.parentElement;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidPrev(currentElement);
    }
    event.stopPropagation();
  } /*end*/ /*pagetoggles-2*/ else if (
    event.target.matches(".mr-widget-radio:not([checked])")
  ) {
    const currentElement = event.target;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      const mrwidLayout = currentElement.closest(".mr-layout");
      const mrwidPage = currentElement.value;
      mrwidChangePage(currentElement, mrwidLayout, mrwidPage);
    }
    event.stopPropagation();
  } /*end*/ /*pagetoggles-1*/ else if (
    event.target.matches(".mr-widget-pageselect")
  ) {
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
  } /*end*/ /*pagetoggles-3*/ else if (
    event.target.matches(".mr-widget-below")
  ) {
    const currentElement = event.target;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidBelow(currentElement);
    }
    event.stopPropagation();
  } else if (event.target.matches(".mr-widget-below span")) {
    const currentElement = event.target.parentElement;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidBelow(currentElement);
    }
    event.stopPropagation();
  } /*end*/ /*pagetoggles-4*/ else if (
    event.target.matches("button.mr-widget-scroll")
  ) {
    const currentElement = event.target;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidBelow(currentElement);
    }
    event.stopPropagation();
  } else if (event.target.matches("button.mr-widget-scroll span")) {
    const currentElement = event.target.parentElement;
    if (!currentElement.classList.contains("loading")) {
      currentElement.classList.add("loading");
      mrwidBelow(currentElement);
    }
    event.stopPropagation();
  } /*end*/ else if (
    event.target.matches(".add_to_cart_button.ajax_add_to_cart")
  ) {
    if (event.target.getAttribute("data-product_id").includes(",")) {
      let productIDs = event.target.getAttribute("data-product_id").split(",");
      let initialProductIDs = event.target
        .getAttribute("data-product_id")
        .split(",");
      if (productIDs) {
        for (id = 0; id < productIDs.length; id++) {
          productIDs = productIDs.slice(1);
          event.target.setAttribute("data-product_id", productIDs);
          event.target.click();
        }
        event.target.setAttribute("data-product_id", initialProductIDs);
        if (
          event.target.getAttribute("data-coupon") &&
          document.getElementById("fkcart-coupon__input")
        ) {
          document.getElementById("fkcart-coupon__input").value =
            event.target.getAttribute("data-coupon");
          document
            .getElementById("fkcart-coupon__input")
            .setAttribute("value", event.target.getAttribute("data-coupon"));
          document.querySelector(".fkcart-coupon-button").click();
        }
      }
    }
  }
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
            currentElement.classList.contains("mr-widget-below")
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
  const mrwidsScroll = document.querySelectorAll("button.mr-widget-scroll");
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
    ".mr-theme .mr-layout.mr-parallax.mr-background .mr-widget-pages.mr-active .mr-image"
  );
  const st = window.pageYOffset;
  mrInitst = st;
  const mrthumbparElements = document.querySelectorAll(
    ".mr-theme .mr-layout.mr-parallax.mr-thumbnail:not(.mr-background) .mr-widget-pages.mr-active .mr-image img"
  );
  if (mrthumbparElements) {
    for (id = 0; id < mrthumbparElements.length; id++) {
      mrthumbparElements[id].style.transform =
        "translateY(-" + st * 0.04 + "px)";
    }
  }
  /*end*/
});
