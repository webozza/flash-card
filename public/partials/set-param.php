<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://webozza.com
 * @since      1.0.0
 *
 * @package    Flash_Card
 * @subpackage Flash_Card/public/partials
 */
?>

<style>
    .portfolio_sets-template-default .post-sidebar {display: none};
</style>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
    $pluginimg = '/wp-content/plugins/flash-card/public/img/';
    // Check Limit
    $setownerid = get_current_user_id();
    $usersetlimit = get_user_meta($setownerid, 'set_creation_limit')[0];
    $usersetcount = count_user_posts($setownerid, 'portfolio_sets')[0];

    // Get Options
    $getoptions = get_option('fc_rlink');
    $redirectid = $getoptions['duplicate_redirect_id'];
    $redirectlink = $getoptions['duplicate_redirect_link'];

    // Other stuff
    $pluginurl = '/wp-content/plugins/flash-card/';
    $fcimg = '/wp-content/plugins/flash-card/public/img/';
    $cardsetid = $sets;
    $carduserid = get_current_user_id();
    $currentUserId = get_current_user_id();
    $duplicateSetId = $cardsetid;
?>

<script>
    let flashcardSettings = {
        nonce: "<?= wp_create_nonce('wp_rest') ?>",
        pluginimg: "<?= $pluginimg ?>",
        cardsetid: "<?= $cardsetid ?>",
        redirectlink: "<?= $redirectlink ?>",
    }
</script>

<?php
        
        // Query the custom cards
        $customcards = array(
            'post_type' => 'portfolio_flashcards',
            'posts_per_page' => -1,
            'order' => 'ASC',
            // 'author' => $carduserid,
            'meta_query' => array(
                array(
                    'key' => 'parent_sets',
                    'value' => $cardsetid
                )
            )
        );
        $custom_cards = new WP_Query( $customcards );
        $totalcustomcards = $custom_cards->post_count;
        
        $getpresetcards = get_post_meta($cardsetid, 'selected_presets')[0];
        $uniquepresetcardids = array();

        foreach($getpresetcards as $dog) {
            foreach($dog['ids'] as $cat) {
                array_push($uniquepresetcardids, $cat);
            }
        }

        $totalpresetcards = count(array_unique($uniquepresetcardids));
        $totalcardsofset = $totalcustomcards + $totalpresetcards;

        // Query the preset cards
        $preset_cards = array (
            'post__in' => $uniquepresetcardids,
            'orderby' => 'post__in',
            'post_status' => 'publish',
            'post_type' => 'portfolio',
            'posts_per_page' => -1,
        );
        
        $presetcards = new WP_Query($preset_cards);
        
    ?>
    <div class="fc-main-container">
        <div class="fc-heading text-center">
            <h1 class="entry-title"><?= get_the_title($cardsetid) ?></h1>
            <p><?= get_post_field('post_content', $cardsetid) ?></p>
        </div>
        <div class="fc-body flashcard_set" style="width:650px;height: 600px;">
            <div id="set-id-<?= $cardsetid ?>" class="text-center flashcard_set-container">
                <!-- Slider main container -->
                <div class="swiper">
                <!-- Additional required wrapper -->
                <div id="fc--swiper" class="swiper-wrapper">
                    <!-- Slides | Start the Loop -->
                    <?php $row = 1; while ( $custom_cards->have_posts() ) : $custom_cards->the_post(); ?>
                    <div class="fc-item card-fc-item swiper-slide">
                        <div class="item">
                            <div id="card" class="card-item fc-custom--card" custom-card-id="<?= get_the_id() ?>">
                            <div class="front">
                                <div class="fav-container">
                                <div class="loader-container">
                                    <div class="loader"></div>
                                </div>
                                <a href="javascript:void(0)" class="fav removed-from-fav">
                                    <svg
                                    aria-hidden="true"
                                    focusable="false"
                                    data-prefix="fas"
                                    data-icon="star"
                                    role="img"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 576 512"
                                    class="svg-inline--fa fa-star fa-w-18 fa-2x"
                                    style="backface-visibility: hidden"
                                    >
                                    <path
                                        no-flip=""
                                        fill="currentColor"
                                        d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"
                                        class=""
                                        style="backface-visibility: hidden"
                                    ></path>
                                    </svg>
                                </a>
                                <div
                                    class="UITooltip UITooltip--micro UITooltip--includeArrowInBounds fav-tooltip"
                                    data-remove="Star card for study later"
                                    data-add="Remove star"
                                    style=""
                                >
                                    <span style="backface-visibility: hidden"
                                    >Star card for study later</span
                                    >
                                </div>
                                </div>
                                <div class="card-text">
                                    <!-- <span class="">Side A</span> -->
                                    <img class="fc--card-thumb swiper-lazy" data-src="<?= get_the_post_thumbnail_url() ?>"></img>
                                </div>
                            </div>
                            <div class="back">
                                <div class="fav-container">
                                <div class="loader-container">
                                    <div class="loader"></div>
                                </div>
                                <a href="javascript:void(0)" class="fav removed-from-fav">
                                    <svg
                                    aria-hidden="true"
                                    focusable="false"
                                    data-prefix="fas"
                                    data-icon="star"
                                    role="img"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 576 512"
                                    class="svg-inline--fa fa-star fa-w-18 fa-2x"
                                    style="backface-visibility: hidden"
                                    >
                                    <path
                                        fill="currentColor"
                                        d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"
                                        class=""
                                        style="backface-visibility: hidden"
                                    ></path>
                                    </svg>
                                </a>
                                <div
                                    class="UITooltip UITooltip--micro UITooltip--includeArrowInBounds fav-tooltip"
                                    data-remove="Star card for study later"
                                    data-add="Remove star"
                                    style=""
                                >
                                    <span style="backface-visibility: hidden"
                                    >Star card for study later</span
                                    >
                                </div>
                                </div>
                                <div class="card-text">
                                    <h3 class="card-title"><?= get_the_title() ?></h3>
                                    <p class="card-description"><?= get_the_content() ?></p>
                                    <p class="card-cat"></p>
                                </div>
                            </div>
                            <div class="loader-container full-screen" style="display: none">
                                <div class="loader"></div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <?php ++$row; endwhile; ?>

                    <!-- Preset Cards Loop -->
                    <?php while ( $presetcards->have_posts() ) : $presetcards->the_post(); ?>
                    <div class="fc-item card-fc-item swiper-slide">
                            <div class="item">
                                <div id="card" class="card-item fc-preset--card" preset-card-id="<?= get_the_ID() ?>">
                                <div class="front">
                                    <div class="fav-container">
                                    <div class="loader-container">
                                        <div class="loader"></div>
                                    </div>
                                    <a href="javascript:void(0)" class="fav removed-from-fav">
                                        <svg
                                        aria-hidden="true"
                                        focusable="false"
                                        data-prefix="fas"
                                        data-icon="star"
                                        role="img"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 576 512"
                                        class="svg-inline--fa fa-star fa-w-18 fa-2x"
                                        style="backface-visibility: hidden"
                                        >
                                        <path
                                            fill="currentColor"
                                            d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"
                                            class=""
                                            style="backface-visibility: hidden"
                                        ></path>
                                        </svg>
                                    </a>
                                    <div
                                        class="UITooltip UITooltip--micro UITooltip--includeArrowInBounds fav-tooltip"
                                        data-remove="Star card for study later"
                                        data-add="Remove star"
                                        style=""
                                    >
                                        <span style="backface-visibility: hidden"
                                        >Star card for study later</span
                                        >
                                    </div>
                                    </div>
                                    <div class="card-text">
                                        <!-- <span class="">Side A</span> -->
                                        <img class="fc--card-thumb swiper-lazy" data-src="<?= get_the_post_thumbnail_url() ?>"></img>
                                    </div>
                                </div>
                                <div class="back">
                                    <div class="fav-container">
                                    <div class="loader-container">
                                        <div class="loader"></div>
                                    </div>
                                    <a href="javascript:void(0)" class="fav removed-from-fav">
                                        <svg
                                        aria-hidden="true"
                                        focusable="false"
                                        data-prefix="fas"
                                        data-icon="star"
                                        role="img"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 576 512"
                                        class="svg-inline--fa fa-star fa-w-18 fa-2x"
                                        style="backface-visibility: hidden"
                                        >
                                        <path
                                            fill="currentColor"
                                            d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"
                                            class=""
                                            style="backface-visibility: hidden"
                                        ></path>
                                        </svg>
                                    </a>
                                    <div
                                        class="UITooltip UITooltip--micro UITooltip--includeArrowInBounds fav-tooltip"
                                        data-remove="Star card for study later"
                                        data-add="Remove star"
                                        style=""
                                    >
                                        <span style="backface-visibility: hidden"
                                        >Star card for study later</span
                                        >
                                    </div>
                                    </div>
                                    <div class="card-text">
                                        <div class="card-title-container">
                                            <h3 class="card-title"><?= get_the_title() ?></h3>
                                            <span class="fc-item-header">Other Item Names:</span>
                                            <p><?= get_the_content() ?></p>
                                        </div>
                                        <div class="card-description-container">
                                            <span class="fc-item-header">Item Description:</span>
                                            <p><?= get_post_meta(get_the_ID(), '_custom_editor_1', true) ?></p>
                                        </div>
                                        <div class="card-cat-container">
                                            <p class="card-cat" data-post-id="<?= get_the_ID() ?>">
                                                <?php 
                                                    $postcats = wp_get_object_terms( get_the_ID(), 'portfolio_entries', array( 'fields' => 'names' ) );
                                                    foreach($postcats as $postcat) {
                                                        echo '<span class="cat-names">'.$postcat.'</span>';
                                                    };
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="loader-container full-screen" style="display: none">
                                    <div class="loader"></div>
                                </div>
                                </div>
                            </div>
                        </div>
                    <?php ++$row; endwhile; ?>
                </div>
                <!-- Flashcard Slide Controls -->
                <div class="flashcard-slide-controls">
                    <div class="fc-slide-controls-left"></div>
                    <div class="fc-slide-controls-middle">
                        <div class="swiper-button-prev">&#8592;</div>
                        <div class="fc-slide-count">
                            <span class="fc-current-card">1</span>
                            <span class="fc-slide-divider">/</span>
                            <span class="fc-total-cards"><?= $totalcardsofset ?></span>
                        </div>
                        <div class="swiper-button-next">&#8594;</div>
                    </div>
                    <div class="fc-slide-controls-right">
                        <a href="javascript:void(0)" class="shuffle-cards" style="display:none">
                            <img src="<?= $fcimg . 'shuffle-icon.png' ?>">
                        </a>
                        <a href="javascript:void(0)" class="shuffle-cards-2">
                            <img src="<?= $fcimg . 'shuffle-icon.png' ?>">
                        </a>
                        <a href="javascript:void(0)" class="switch-cards">
                            <img src="<?= $fcimg . 'switch-icon.png' ?>">
                        </a>
                    </div>
                </div>
                <!-- Flashcard Slide Controls -->
                <?php if($totalcardsofset > 0 && ($usersetcount < $usersetlimit || $usersetlimit == "")) { ?>
                    <a id="duplicateSet" href="javascript:void(0)" class="button primary" style="margin-top:20px;">
                        <span>Duplicate Set</span>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>

  <script>
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
      mapKeys.map((entries) => {
        portfolioIds.push(...entries.ids);
        uniquePortfolioIds = [...new Set(portfolioIds)];
        portfolioCats.push(entries);
      });

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

      var switchControl = () => {
        var cards = $(".swiper-slide .card-item");
        var switchCntrl = $(".switch-cards");
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
      switchControl();

      let reStudy = () => {
        $(".btn-redo").on("click", function (e) {
          e.preventDefault();
          $(".fav").removeClass("added-to-fav").addClass("removed-from-fav");
          swiper.slideTo(0);
          swiper.removeSlide(allSlidesIndex);
          allSlides.map((entries) => {
            swiper.appendSlide(entries[0].outerHTML);
            setTimeout(function () {
              switchControl();
            }, 500);
          });
          let getNewTotal = $(".swiper-slide").length;
          $(".fc-total-cards").text(getNewTotal - 1);
          reStudy();
          starCards();
          studyStarred();
          reachEnd();
          $(".switch-cards").removeClass("active");
          $(".swiper-slide .card-item").removeClass("active");
        });
      };
      reStudy();

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

    /* Duplicate cards
        ---------------------------------------------------------- */
    let duplicateSetId;
    let duplicatedSetId;

    let fetchCopyCards = async () => {
      const url = `/wp-json/wp/v2/portfolio_flashcards?per_page=100`;
      let res = await fetch(url, {
        method: "GET",
        headers: {
          "Content-type": "application/json; charset=UTF-8",
          "X-WP-Nonce": flashcardSettings.nonce,
        },
      });
      return await res.json();
    };

    let renderCopyCards = async () => {
      let copyCardsData = await fetchCopyCards();
      await copyCardsData.map(async (entries) => {
        if (entries.meta.parent_sets == flashcardSettings.cardsetid) {
          let copiedTitle = await entries.title.rendered;
          let copiedContent = await entries.content.rendered;
          let formatCopiedContent = await copiedContent.slice(
            3,
            copiedContent.length - 5
          );
          let copiedThumb = await entries.featured_media;

          // post new cards taking the data from copy
          let createNewCardsFromCopy = async () => {
            const url = `/wp-json/wp/v2/portfolio_flashcards/`;
            let res = await fetch(url, {
              method: "POST",
              headers: {
                "Content-type": "application/json; charset=UTF-8",
                "X-WP-Nonce": flashcardSettings.nonce,
              },
              body: JSON.stringify({
                title: await copiedTitle,
                content: await formatCopiedContent,
                featured_media: await copiedThumb,
                meta: {
                  parent_sets: `${await duplicatedSetId}`,
                },
                status: "publish",
              }),
            });
            return await res.json();
          };
          let renderCreationFromCopy = async () => {
            let logNewCardData = await createNewCardsFromCopy();
          };
          await renderCreationFromCopy();
        }
      });
    };

    /* Duplicate set
        ---------------------------------------------------------- */

    let fetchDuplicateSet = async () => {
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

    let duplicateSet = () => {
      $("#duplicateSet").click(async function () {
        await renderDuplicateSet();
        refreshPage();
      });
    };

    var _createCopy = {
      title: "",
      content: "",
      status: "publish",
      featured_media: "",
      meta: {
        selected_presets: [],
      },
    };

    let fetchSetCopy = async () => {
      const url = `/wp-json/wp/v2/portfolio_sets/`;
      let res = await fetch(url, {
        method: "POST",
        headers: {
          "Content-type": "application/json; charset=UTF-8",
          "X-WP-Nonce": await flashcardSettings.nonce,
        },
        body: JSON.stringify(_createCopy),
      });
      return await res.json();
    };

    let renderDuplicateSet = async () => {
      let duplicateSetData = await fetchDuplicateSet();
      let formatDuplicatedContent = await duplicateSetData.content.rendered;
      let formattedContent = await formatDuplicatedContent.slice(
        3,
        formatDuplicatedContent.length - 5
      );
      _createCopy.title = `${await duplicateSetData.title.rendered} copy`;
      _createCopy.content = `${await formattedContent}`;
      _createCopy.featured_media = `${await duplicateSetData.featured_media}`;

      // map and push for duplicating preset cards for duplicated set
      let selectedCat = await duplicateSetData.meta.selected_presets;
      await selectedCat.map((entries) => {
        _createCopy.meta.selected_presets.push(entries);
      });

      // append the new set
      let newSetCopy = await fetchSetCopy();
      duplicatedSetId = await newSetCopy.id;
    };

    let refreshPage = async () => {
      await renderCopyCards();
      window.location.href = flashcardSettings.redirectlink;
    };

    duplicateSet();

    $(".card-title-container").each(function () {
      let eachOIN = $(this);
      let checkEmpty = eachOIN.find("p").text() == "";
      if (checkEmpty) {
        eachOIN.find(".fc-item-header").remove();
      }
    });
  });
})(jQuery);
</script>

