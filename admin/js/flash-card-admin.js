(function ($) {
  $(window).load(() => {
    // Validations
    let postPage =
      $("body").hasClass("post-type-portfolio_sets") &&
      $("body").hasClass("post-php");
    let newPostPage =
      $("body").hasClass("post-type-portfolio_sets") &&
      $("body").hasClass("post-new-php");
    let pcCount = [];
    let ccCount = [];
    let currentPostID = $("#post_ID").val();
    let presetsRendered = false;

    /* Get Nonce / Security
    ------------------------------------------------------------------------*/
    let getNonce = () => {
      $("body").append(`<input id="getNonce" type="hidden" value="" />`);
    };

    /* Switches value based on admin action
     * ---------------------------------------------------------------------*/
    let updateFeaturedCheckbox = () => {
      $("#featured_set_checkbox").change(function () {
        if ($(this).find("input").is(":checked")) {
          $(this).find("input").val("true");
        } else {
          $(this).find("input").val("false");
        }
      });
    };

    /* All Sets Backend Edit Page
     * ---------------------------------------------------------------------*/
    let fetchAllSets = async () => {
      const url = `/wp-json/wp/v2/portfolio_sets`;
      let res = await fetch(url);
      return await res.json();
    };

    let checkAllSets = async () => {
      let allSets = await fetchAllSets();
      if ($(".no-items").length !== 1) {
        $(".wp-list-table thead tr").append("<th>Featured</th>");
        $("#the-list tr").append(
          "<td><span class='featured-set-checkbox'><input class='star' type='checkbox'></span></td>"
        );
      }

      allSets.map((entries) => {
        if (entries.meta.featured_set[0] == "true") {
          var findFeaturedSets = entries.id;
          $(
            `#the-list tr[id='post-${findFeaturedSets}'] td:last-child input`
          ).attr("checked", "");
        }
      });

      updateSetMetas();
    };

    var _updateSetMetas = {
      meta: {
        featured_set: "",
      },
    };

    var updateSetId;
    let getSet = async () => {
      const url = `/wp-json/wp/v2/portfolio_sets/${updateSetId}`;
      let res = await fetch(url, {
        method: "PATCH",
        headers: {
          "X-WP-Nonce": flashcardSettings.nonce,
          "Content-type": "application/json; charset=UTF-8",
        },
        body: JSON.stringify(_updateSetMetas),
      });
      return await res.json();
    };

    let updateSet = async () => {
      let updateSet = await getSet();
      console.log(updateSet);
    };

    let updateSetMetas = async () => {
      $(".featured-set-checkbox").on("click", function () {
        var checkbox = $(this).find("input");
        if (checkbox[0].checked == true) {
          checkbox.attr("checked", "");
          _updateSetMetas.meta.featured_set = "true";
        } else {
          checkbox.removeAttr("checked", "");
          _updateSetMetas.meta.featured_set = "false";
        }
        updateSetId = checkbox.parent().parent().parent().attr("id").slice(5);
        updateSet();
      });
    };

    /* Get all users and set owner
     * ---------------------------------------------------------------------*/

    let renderAllUsers = async () => {
      let currentOwnerID = $("#post_author").val();
      $("#set_owner").select2({
        placeholder: "a short description of what this content explains",
        minimumInputLength: 2,
        tags: false,
      });
      $("#set_owner").val(currentOwnerID).trigger("change");
      $("#set_owner").on("select2:select", function (e) {
        let selectedOwnerID = $(this).val();
        $("#post_author").val(selectedOwnerID);
      });
    };

    /* Just checking data schema
     * ---------------------------------------------------------------------*/
    let fetchCustomCards = async () => {
      const url = `/wp-json/wp/v2/portfolio_sets`;
      let res = await fetch(url, {
        headers: {
          "X-WP-Nonce": wpApiSettings.nonce,
        },
      });
      return await res.json();
    };

    let checkCards = async () => {
      let customCards = await fetchCustomCards();
    };

    /* New Custom Card Html excutes when custom card is added
     * ---------------------------------------------------------------------*/
    let cardHtml = () => {
      $("#accordion_new_card").append(`
        <div class="accordion-inner" style="display: none;">
          <div class="accordion-header">
            <div>
              <span style="color:blue;">^</span>
              <h3>New Card</h3>
            </div>
            <span style="color:red;">X</span>
          </div>
          <div class="accordion-body">
            <h3>Side A</h3>
            <div class="cc_field">
              <p>Title of card</p>
              <input type="text" class="cc--title">
            </div>
            <div class="cc_field">
              <p>Card description </p>
              <textarea type="text" class="cc--desc"></textarea>
            </div>
            <h3>Side B</h3>
            <div class="cc_field_image">
              <a href="#" class="button button-primary rudr-upload">Upload image</a>
              <a href="#" class="rudr-remove" style="display:none">Remove image</a>
              <input type="hidden" name="rudr_img" value="">
            </div>
            <div class="save--card">
              <a href="javascript:void(0)">Save Card</a>
            </div>
          </div>
        </div>
      `);
      setTimeout(function () {
        $("#accordion_new_card > div").slideDown();
      }, 100);
    };

    /* WP Sidebar menu handles
     * ---------------------------------------------------------------------*/
    let existingCardsToggle = () => {
      $(".existing-cards .accordion-header").click(function () {
        $(this).next().slideToggle();
      });
    };

    let disableAddNew = () => {
      $("#menu-posts-portfolio_flashcards > ul li").each(function () {
        let getLinks = $(this).find("a").attr("href");
        if (getLinks === "post-new.php?post_type=portfolio_flashcards") {
          $(this).remove();
        }
      });
    };

    let addSetsMenu = () => {
      $("#menu-posts-portfolio_flashcards > ul li.wp-first-item").after(`
        <li class="wp-second-item">
          <a href="edit.php?post_type=portfolio_sets" class="wp-second-item">Sets</a>
        </li>
      `);
    };

    let setIsActive = () => {
      let checkUri = window.location.href.includes("?post_type=portfolio_sets");
      let flashcardMenu = $("#menu-posts-portfolio_flashcards");
      if (checkUri) {
        $("#menu-posts-portfolio_flashcards > ul li.wp-second-item").addClass(
          "current"
        );
        flashcardMenu.addClass("wp-menu-open wp-has-current-submenu");
        flashcardMenu.removeClass("wp-not-current-submenu");
      }
    };

    /* Image uploader for custom cards
     * ---------------------------------------------------------------------*/

    let webozzaUploader = async () => {
      $("body").on("click", ".rudr-upload", function (event) {
        event.preventDefault(); // prevent default link click and page refresh

        const button = $(this);
        const imageId = button.next().next().val();

        const customUploader = wp
          .media({
            title: "Insert image", // modal window title
            library: {
              // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
              type: "image",
            },
            button: {
              text: "Use this image", // button label text
            },
            multiple: false,
          })
          .on("select", function () {
            // it also has "open" and "close" events
            const attachment = customUploader
              .state()
              .get("selection")
              .first()
              .toJSON();
            button
              .removeClass("button button-primary")
              .html('<img src="' + attachment.url + '">'); // add image instead of "Upload Image"
            button.next().show(); // show "Remove image" link
            button.next().next().val(attachment.id); // Populate the hidden field with image ID
          });

        // already selected images
        customUploader.on("open", function () {
          if (imageId) {
            const selection = customUploader.state().get("selection");
            attachment = wp.media.attachment(imageId);
            attachment.fetch();
            selection.add(attachment ? [attachment] : []);
          }
        });

        customUploader.open();
      });
      // on remove button click
      $("body").on("click", ".rudr-remove", function (event) {
        event.preventDefault();
        const button = $(this);
        button.next().val(""); // emptying the hidden field
        button
          .hide()
          .prev()
          .addClass("button button-primary")
          .html("Upload image"); // replace the image with text
      });
    };

    /* Creates new custom cards
     * ---------------------------------------------------------------------*/
    var _data = {
      title: "",
      status: "publish",
      content: "",
      meta: {
        parent_sets: [],
      },
      featured_media: "",
    };

    let createCustomCards = async () => {
      const url = `/wp-json/wp/v2/portfolio_flashcards`;
      let res = await fetch(url, {
        method: "POST",
        body: JSON.stringify(_data),
        headers: {
          "X-WP-Nonce": wpApiSettings.nonce,
          "Content-type": "application/json; charset=UTF-8",
        },
      });
      return await res.json();
    };

    let postCustomCard = async () => {
      let customCard = await createCustomCards();

      // Sets Post ID
      // $("#accordion_new_card > div").each(function () {
      //   let thisCard = $(this);
      //   let checkId = thisCard.attr("id");
      //   if (checkId == undefined) {
      //     thisCard.attr("id", customCard.id);
      //   }
      // });
    };

    /* Save new custom cards
     * ---------------------------------------------------------------------*/
    let saveCard = async () => {
      $("#accordion_new_card > div:last-child .save--card a").click(
        async function () {
          let cardTitleVal = $(this).parent().parent().find(".cc--title").val();
          let cardDescVal = $(this).parent().parent().find(".cc--desc").val();
          let featuredImgID = $(this)
            .parent()
            .parent()
            .find('[name="rudr_img"]')
            .val();
          let setId = $("#post_ID").val();
          $(this)
            .parent()
            .parent()
            .parent()
            .find(".accordion-header h3")
            .text(cardTitleVal)
            .fadeIn();
          _data.title = cardTitleVal;
          _data.content = cardDescVal;
          _data.featured_media = featuredImgID;
          if (_data.meta.parent_sets == "") {
            _data.meta.parent_sets.push(setId);
          }
          if ($(this).text() !== "Update Card") {
            postCustomCard();
            $(this).parent().attr("class", "update--card");
            $(this).text("Update Card");
            updateCards();
          } else {
          }
        }
      );
    };

    /* Updated existing custom cards
     * ---------------------------------------------------------------------*/

    var _updateData = {
      title: "",
      status: "publish",
      content: "",
      featured_media: "",
    };

    var updateId;

    let prepareUpdate = async () => {
      const url = `/wp-json/wp/v2/portfolio_flashcards/${updateId}`;
      let res = await fetch(url, {
        method: "PUT",
        body: JSON.stringify(_updateData),
        headers: {
          "X-WP-Nonce": wpApiSettings.nonce,
          "Content-type": "application/json; charset=UTF-8",
        },
      });
      return await res.json();
    };

    let updateCustomCards = async () => {
      let updateCards = await prepareUpdate();
    };

    let updateCards = async () => {
      $(".update--card a").on("click", async function () {
        let selectedUpdate = $(this).parent().parent().parent();
        updateId = $(this).parent().parent().parent().attr("id");
        _updateData.title = selectedUpdate.find(".cc--title").val();
        _updateData.content = selectedUpdate.find(".cc--desc").val();
        let updateFeaturedImgId = $(this)
          .parent()
          .parent()
          .find('[name="rudr_img"]')
          .val();
        _updateData.featured_media = updateFeaturedImgId;
        selectedUpdate
          .find(".accordion-header > div h3")
          .text(`${selectedUpdate.find(".cc--title").val()}`);
        updateCustomCards();
      });
    };

    let accordionToggle = () => {
      $("#accordion_new_card > div:last-child .accordion-header").click(
        function () {
          $(this).next().slideToggle();
        }
      );
    };

    /* Render existing custom cards if any
     * ---------------------------------------------------------------------*/
    let fetchCardMeta = async () => {
      const url = `/wp-json/wp/v2/portfolio_flashcards/?per_page=100`;
      let res = await fetch(url);
      return await res.json();
    };

    let featuredMediaId;
    let featchSourceUrl = async () => {
      const url = `/wp-json/wp/v2/media/${featuredMediaId}`;
      let res = await fetch(url, {
        method: "GET",
        headers: {
          "X-WP-Nonce": wpApiSettings.nonce,
          "Content-type": "application/json; charset=UTF-8",
        },
      });
      return await res.json();
    };

    let renderFeaturedImg = async () => {
      let featuredMedias = await featchSourceUrl();
      $(`.existing-cards img[featured_media_id="${featuredMedias.id}"]`).attr(
        "src",
        featuredMedias.source_url
      );
    };

    let renderCardMeta = async () => {
      let cardMetas = await fetchCardMeta();
      cardMetas.map((entries) => {
        if (entries.meta.parent_sets == $("#post_ID").val()) {
          let renderedContent = entries.content.rendered;
          let formatContent = renderedContent.slice(
            3,
            renderedContent.length - 5
          );
          $("#accordion_new_card").append(`
            <div class="accordion-inner existing-cards" id="${entries.id}">
              <div class="accordion-header">
                <div>
                  <span style="color:blue;">^</span>
                  <h3>${entries.title.rendered}</h3>
                </div>
                <span style="color:red;">X</span>
              </div>
              <div class="accordion-body" style="display:none">
                <h3>Side A</h3>
                <div class="cc_field">
                  <p>Title of card</p>
                  <input type="text" class="cc--title" value="${entries.title.rendered}">
                </div>
                <div class="cc_field">
                  <p>Card description </p>
                  <textarea type="text" class="cc--desc">${formatContent}</textarea>
                </div>
                <h3>Side B</h3>
                <div class="cc_field_image">
                  <a href="#" class="rudr-upload"><img featured_media_id="${entries.featured_media}" src=""></a>
                  <a href="#" class="rudr-remove" style="">Remove image</a>
                  <input type="hidden" name="rudr_img" value="${entries.featured_media}">
                </div>
                <div class="update--card">
                  <a href="javascript:void(0)">Update Card</a>
                </div>
              </div>
            </div>
          `);

          // Set featured image link
          $("#accordion_new_card .existing-cards").each(function () {
            featuredMediaId = $(this).find('input[name="rudr_img"]').val();
            renderFeaturedImg();
          });

          // show first card
          $(
            "#accordion_new_card .existing-cards:first-child .accordion-body"
          ).slideDown();
        } else {
          //$("#add_new_card").remove();
        }
      });

      // toggle for existing cards
      $(".existing-cards .accordion-header").click(function () {
        $(this).next().slideToggle();
      });
      deleteCard();
      updateCards();
      webozzaUploader();
    };

    /* Delete Custom card
     * ---------------------------------------------------------------------*/
    var deleteId;

    let prepareDelete = async () => {
      const url = `/wp-json/wp/v2/portfolio_flashcards/${deleteId}`;
      let res = await fetch(url, {
        method: "DELETE",
        headers: {
          "X-WP-Nonce": wpApiSettings.nonce,
          //"Content-type": "application/json; charset=UTF-8",
        },
      });
      return await res.json();
    };

    let deleteCustomCards = async () => {
      let deleteCards = await prepareDelete();
    };

    let deleteCard = async () => {
      $("#accordion_new_card > div .accordion-header > span").on(
        "click",
        function (e) {
          e.preventDefault();
          let selectedAccordion = $(this).parent().parent();

          deleteId = selectedAccordion.attr("id");
          deleteCustomCards();
          $(this).parent().parent().fadeOut().remove();
          if (selectedAccordion.attr("id") == undefined) {
            selectedAccordion.fadeOut().remove();
          }
          $("#accordion_new_card > div .accordion-header > span").css(
            "pointer-events",
            "none"
          );
          setTimeout(function () {
            $("#accordion_new_card > div .accordion-header > span").css(
              "pointer-events",
              "auto"
            );
          }, 200);
        }
      );
    };

    /* Save Presets
     * ------------------------------------------------------- */
    var _presetData = {
      meta: {
        selected_presets: [],
      },
    };

    let fetchSavedPresets = async () => {
      const url = `/wp-json/wp/v2/portfolio_sets/${currentPostID}`;
      let res = await fetch(url, {
        method: "GET",
        headers: {
          "X-WP-Nonce": wpApiSettings.nonce,
        },
      });
      return await res.json();
    };

    let renderSavedPresets = async () => {
      let savedPresets = await fetchSavedPresets();
      let selectedPresetRecords = savedPresets.meta.selected_presets;
      selectedPresetRecords.map((entries) => {
        $(`#${entries.cat}`).val(entries.ids).trigger("change");
      });
      // Validation
      pcCount.push($(".preset-selection :selected").length);
    };

    let fetchPostPresets = async () => {
      let thisSetId = $("#post_ID").val();
      const url = `/wp-json/wp/v2/portfolio_sets/${thisSetId}`;
      let res = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": wpApiSettings.nonce,
        },
        body: JSON.stringify(_presetData),
      });
      return await res.json();
    };

    let setSlug = () => {
      let editableSlug = $("#title").val();
      $("#editable-post-name").text(editableSlug);
    };

    let firePresets = () => {
      $(".preset-selection").select2({
        templateResult: formatState,
        templateSelection: formatState,
      });
      function formatState(opt) {
        if (!opt.id) {
          return opt.text.toUpperCase();
        }
        var optimage = $(opt.element).attr("data-img_url");
        if (!optimage) {
          return opt.text.toUpperCase();
        } else {
          var $opt = $(
            '<span><img src="' +
              optimage +
              '" width="60px" + height=`60px` /> ' +
              opt.text.toUpperCase() +
              "</span>"
          );
          return $opt;
        }
      }
      $(".preset-selection").on("select2:unselect", function (e) {
        $(this).clear();
      });
    };

    let _fcPresetData = [];

    let makePresetArray = () => {
      $(".save-preset").each(function () {
        let selectedCat = $(this).parent().find("select").attr("id");
        let selectedPresets = $(this).parent().find("select").find(":selected");
        let selectedPresetValues = [];
        selectedPresets.each(function () {
          selectedPresetValues.push($(this).val());
        });

        // publish the selection
        _fcPresetData.push({
          cat: selectedCat,
          ids: selectedPresetValues,
        });
      });
      console.log(_fcPresetData);
      $('[name="save_preset_cards"]').val(JSON.stringify(_fcPresetData));
    };

    let updatePresetArray = () => {
      $(".preset-selection").change(function () {
        _fcPresetData = [];
        makePresetArray();
      });
    };

    /* Initiate functions
     * ---------------------------------------------------------------------*/
    updateFeaturedCheckbox();
    disableAddNew();
    addSetsMenu();
    setIsActive();
    renderCardMeta();
    firePresets();

    // If editing sets
    if (postPage || newPostPage) {
      setSlug();
      makePresetArray();
      updatePresetArray();

      var newCustomCards = [];
      var existingCustomCards = [];

      //new set cards
      $("#submitdiv").hover(function () {
        newCustomCards = [];
        existingCustomCards = [];
        $("#accordion_new_card .accordion-inner.existing-cards").each(
          function () {
            existingCustomCards.push({
              post_title: $(this).find("input.cc--title").val(),
              post_desc: $(this).find("textarea.cc--desc").val(),
              thumb_id: $(this).find('input[name="rudr_img"]').val(),
              post_id: $(this).attr("id"),
            });
          }
        );
        $('[name="save_existing_cards"]').val(
          JSON.stringify(existingCustomCards)
        );
        $("#accordion_new_card .accordion-inner:not(.existing-cards)").each(
          function () {
            newCustomCards.push({
              post_title: $(this).find("input.cc--title").val(),
              post_desc: $(this).find("textarea.cc--desc").val(),
              thumb_id: $(this).find('input[name="rudr_img"]').val(),
              post_id: $(this).attr("id"),
            });
          }
        );
        $('[name="save_new_cards"]').val(JSON.stringify(newCustomCards));
      });

      $(".cc--new .accordion-header").click(async function () {
        $(this).next().slideToggle();
      });
      $("#mcc-rest button#add_new_card").on("click", async function (e) {
        e.preventDefault();
        $(".accordion-body").slideUp();
        cardHtml();
        accordionToggle();
        saveCard();
        deleteCard();
      });
    }

    // if all sets backend page
    if (
      $("body").hasClass("post-type-portfolio_sets") &&
      $("body").hasClass("edit-php") &&
      !$("body").hasClass("post-php")
    ) {
      checkAllSets();
      getNonce();
    }
  });
})(jQuery);
