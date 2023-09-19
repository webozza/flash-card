(function ($) {
  $(window).on("load", function () {
    /* Initialize Swiper
    --------------------------------------------------------------------*/
    const swiper = new Swiper(".swiper", {
      on: {
        afterInit: function () {
          $(".swiper-slide:not(:first-child)").find(".card-text").append(`
            <div class="creating-set-loader swiper-lazy-preloader swiper-lazy-preloader-white">
                <img src="${flashcardSettings.pluginimg}loader.png">
            </div>
        `);
        },
      },

      // Optional parameters
      direction: "horizontal",
      loop: false,
      slidesPerView: 1,
      lazy: {
        loadOnTransitionStart: true,
        loadPrevNext: true,
        loadPrevNextAmount: 2,
      },

      // Navigation arrows
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
    });

    /* Global Variables that store formatted data
    ------------------------------------------------------------------------*/
    let portfolioIds = [];
    let uniquePortfolioIds = [];
    let portfolioCats = [];

    /* The results
  --------------------------------------------------------------------------*/
    swiper.appendSlide(`
      <div class="fc-item card-fc-item swiper-slide study__end">
          <div class="item">
              <div id="fc-finished-studying" class="card-item">
                  <div class="studyend-front">
                      <div class="fc-text-wrapper">
                          <div class="congrats-msg">Nice work!</div>
                          <div class="total-studied"></div>
                      </div>
                      <div class="fc-btn-wrapper">
                          <button class="button primary btn-study">Study <span></span> starred items</button>
                          <button style="margin-top:30px" class="button primary btn-redo">Start over</button>
                      </div>
                  </div>
              <div>
          </div>
      </div>
  `);

    /* Get Portfolio Item Ids for this Set
    ------------------------------------------------------------------------*/
    let fetchPortfolioIds = async () => {
      const url = `/wp-json/wp/v2/portfolio_sets/${flashcardSettings.cardsetid}`;
      let res = await fetch(url, {
        method: "GET",
        headers: {
          "Content-type": "application/json; charset=UTF-8",
          "X-WP-Nonce": flashcardSettings.nonce,
        },
      });
      return await res.json();
    };
    let renderPortfolioIds = async () => {
      let presetData = await fetchPortfolioIds();
      let mapKeys = presetData.meta.selected_presets;
      if (mapKeys !== null) {
        mapKeys.map((entries) => {
          portfolioIds.push(...entries.ids);
          uniquePortfolioIds = [...new Set(portfolioIds)];
          portfolioCats.push(entries);
        });
      }

      if (uniquePortfolioIds.length > 0) {
        $(".card-text p").addClass("noclasser");
      }

      /* Duplicate set button
            -------------------------------------------------*/

      let reachEnd = () => {
        swiper.on("reachEnd", function () {
          let getCount = $(".fc-current-card").text();
          var totalStudiedCount = $(".swiper-slide").length - 1;
          let starredCount = $(".added-to-fav").length;

          $(".total-studied").text(
            `You just studied ${totalStudiedCount} cards!`
          );
          setTimeout(() => {
            $(".fc-current-card").text(getCount);
            $(".btn-study span").text(`${starredCount / 2}`);
            if (totalStudiedCount == 1) {
              $(".total-studied").text(
                `You just studied ${totalStudiedCount} card!`
              );
            }
          }, 1);

          if (starredCount > 0) {
            $(".button.primary.btn-study").show();
          } else {
            $(".button.primary.btn-study").hide();
          }
          if (starredCount === 2) {
            $(".button.primary.btn-study").text("");
            $(".button.primary.btn-study").append(
              `Study <span>1</span> starred card`
            );
          } else {
            $(".button.primary.btn-study").text("");
            $(".button.primary.btn-study").append(
              `Study <span>${starredCount}</span> starred cards`
            );
          }
        });
      };
      reachEnd();

      // Hide if empty descriptions
      $(".swiper-slide").each(function () {
        let itemDesc = $(this).find(".fc-preset--card .card-title-container");
        let itemDesc2 = $(this).find(
          ".fc-preset--card .card-description-container"
        );
        if (itemDesc.find("p").length == 0) {
          itemDesc.find("span").remove();
        }
        if (itemDesc2.find("p").text() == "") {
          itemDesc2.find("span").remove();
        }
      });

      let slidesToRemove = [];
      let allSlides = [];
      let allSlidesIndex = [];

      $(".swiper-slide").each(function () {
        let slides = $(this);
        allSlides.push(slides);

        let slidesIndex = $(this).index();
        allSlidesIndex.push(slidesIndex);
      });

      let switchControl = () => {
        let cards = $(".swiper-slide .card-item");
        let switchCntrl = $(".switch-cards");
        switchCntrl.on("click", function () {
          switchCntrl.toggleClass("active");
          if (cards.hasClass("active")) {
            cards.removeClass("active");
            cards.addClass("active");
          } else {
            cards.addClass("active");
          }
          if (switchCntrl.hasClass("active")) {
            cards.addClass("active");
          } else {
            cards.removeClass("active");
          }
        });
      };
      // switchControl();

      let resetCards = async () => {
        let switchCntrl = $(".switch-cards");
        let cards = $(".swiper-slide .card-item");
        if (switchCntrl.hasClass("active")) {
          switchCntrl.removeClass("active");
        }
        if (cards.hasClass("active")) {
          cards.removeClass("active");
        }
        switchControl();
        switchControl();
      };

      let reStudy = () => {
        $(".btn-redo").on("click", function (e) {
          e.preventDefault();
          $(".fav").removeClass("added-to-fav").addClass("removed-from-fav");
          swiper.slideTo(0);
          swiper.removeSlide(allSlidesIndex);
          allSlides.map((entries) => {
            swiper.appendSlide(entries[0].outerHTML);
          });
          resetCards();
          let getNewTotal = $(".swiper-slide").length;
          $(".fc-total-cards").text(getNewTotal - 1);
          starCards();
          studyStarred();
          reachEnd();
          reStudy();
        });
      };
      reStudy();

      // 		$('.btn-redo').click(function() {

      // 		  if (clicks) {
      // 			 switchControl();
      // 		  } else {
      // 			 switchControl();
      // 		  }
      // 		  $(this).data("clicks", !clicks);
      // 		});

      /* Study starred button function
            ------------------------------------------------------------------------*/
      let studyStarred = () => {
        $(".btn-study").click(async function () {
          let slidesToRemove = [];

          $(".removed-from-fav").each(function () {
            let getHtml = $(this).parent().parent().parent().parent().parent();
            let getIndexes = getHtml.index();
            slidesToRemove.push(getIndexes);
          });

          swiper.slideTo(0);

          swiper.removeSlide(slidesToRemove);

          let newTotalCards = $(".swiper-slide").length;
          $(".fc-total-cards").text(newTotalCards - 1);

          $(".fav").removeClass("added-to-fav").addClass("removed-from-fav");

          if ($(".added-to-fav").length == 0) {
            $(".btn-study").hide();
          } else {
            $(".btn-study").show();
          }

          $('.card-item.active').removeClass('active');

        });
      };
      studyStarred();

      /* Flashcard Controls
            ------------------------------------------------------------------------*/
      let totalPresetCards = $(".fc-item.card-fc-item").length;
      if (totalPresetCards == 0) {
        $(".flashcard-slide-controls").remove();
      } else {
        $(".fc-current-card").text("1");
      }
      $(".fc-total-cards").text(totalPresetCards - 1);
      $(".fc-slide-divider").text("/");

      let starCards = () => {
        let cards = $(".fc-item .card-item");
        cards.click(function (e) {
          if (
            e.target.classList[0] !== undefined &&
            e.target.classList[0] !== "fav-container" &&
            e.target.classList[0] !== "fav" &&
            e.target.classList[0] !== "svg-inline--fa"
          ) {
            $(this).toggleClass("active");
          } else {
            $(this)
              .find(".fav")
              .toggleClass("removed-from-fav")
              .toggleClass("added-to-fav");
          }
        });
      };
      starCards();

      // Adding the Active Swiper Slide Index
      swiper.on("slideChange", function () {
        $(".fc-current-card").text(swiper.activeIndex + 1);
      });

      $(".shuffle-cards").click(async function () {
        // $(this).toggleClass("active");
        var container = $(".swiper-wrapper"); // Container
        var nodes = container.find(".swiper-slide:not(.study__end)"); // All children
        var studyEnd = container.find(".swiper-slide.study__end");
        for (var i = 1; i < nodes.length; i++) {
          // Move random child to the end
          container.append(nodes.eq(Math.floor(Math.random() * nodes.length)));
          container.append(studyEnd);
          swiper.slideTo(0);
        }
        container.children().each(async function () {
          let slideImages = $(this).find("img");
          let slideImageLink = slideImages.data("src");
          slideImages.attr("src", slideImageLink);
          slideImages.removeAttr("data-src");
          $(".swiper-slide .creating-set-loader").remove();
        });
      });

      // NEW SHUFFLE FUNCTION
      shuffleActive = false;

      let initialSlides = [];
      $(".swiper-slide:first-child").addClass("first-card-of-deck");
      initialSlides.push($(".swiper-slide.first-card-of-deck"));
      let firstCardOfDeck = initialSlides[0][0].outerHTML;

      let shuffleDeck = () => {
        $(".shuffle-cards").trigger("click");
        shuffleActive = true;
      };

      let defaultDeck = () => {
        var container = $(".swiper-wrapper"); // Container
        var nodes = container.find(
          ".swiper-slide:not(.study__end):not(.first-card-of-deck)"
        );
        var nodeFirstCard = container.find(".swiper-slide.first-card-of-deck");
        var studyEnd = container.find(".swiper-slide.study__end");

        for (var i = 1; i < nodes.length; i++) {
          // Move random child to the end
          container.append(nodes.eq(Math.floor(Math.random() * nodes.length)));
          container.prepend(nodeFirstCard);
          container.append(studyEnd);
          swiper.slideTo(0);
        }

        shuffleActive = false;
      };

      let toggleShuffleDefault = () => {
        shuffleActive ? defaultDeck() : shuffleDeck();
      };

      $(".shuffle-cards-2").click(function () {
        $(this).toggleClass("active");
        swiper.slideTo(0);
        toggleShuffleDefault();
        let cardState = $(
          '.swiper-slide:not(".study__end") .card-item'
        ).hasClass("active");
        if (cardState) {
          $('.swiper-slide:not(".study__end") .card-item').removeClass(
            "active"
          );
        }
      });
    };

    renderPortfolioIds();

    /* Add edit button to wp admin bar
    ------------------------------------------------*/
    if ($("#wpadminbar").length > 0) {
      $("#wp-admin-bar-root-default").append(`
                <li id="wp-admin-bar-edit-set">
                    <a class="ab-item" href="/wp-admin/post.php?post=${flashcardSettings.cardsetid}&action=edit">Edit Set</a>
                </li>
            `);
    }

    /* Remove empty p tags
    ---------------------------------------------------------*/
    let duplicateSingleSet = () => {
      $("#duplicateSet").click(function () {
        $(this).prev().submit();
      });
    };
    duplicateSingleSet();

    /* Remove empty p tags
    ---------------------------------------------------------*/

    $(".card-title-container").each(function () {
      let eachOIN = $(this);
      let checkEmpty = eachOIN.find("p").text() == "";
      if (checkEmpty) {
        eachOIN.find(".fc-item-header").remove();
      }
    });
  });
})(jQuery);
