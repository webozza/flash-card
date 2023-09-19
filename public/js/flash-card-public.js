(function ($) {
  let hostname = window.location.hostname;
  let pathname = window.location.pathname;

  /* Webozza Uploader
   * ------------------------------------------------------- */
  let webozzaUploader = async () => {
    $(
      ".custom-cards:not(.new-card) .rudr-upload, .custom-cards .new-card .rudr-upload"
    ).click(function (e) {
      e.preventDefault(); // prevent default link click and page refresh

      const button = $(this);
      const imageId = button.next().next().val();

      const customUploader = wp
        .media({
          title: "Insert image", // modal window title
          library: {
            // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
            type: "image",
            author: currentUserId,
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

  /* Custom Cards Toggle
   * ------------------------------------------------------- */
  let ccToggleNew = () => {
    $(".custom-cards .new-card:last-child .accordion-title").click(function () {
      $(this).next().slideToggle("fast");
      $(this).toggleClass("active");
    });
    $(".custom-cards .new-card:last-child .accordion-title").next().slideDown();
  };

  /* Add New Card in Frontend
   * ------------------------------------------------------- */
  let newCustomCard = async () => {
    $("#addNewCard").click(function () {
      $(".flash-card-edit-wrapper .custom-cards .accordion").append(`
          <div id="custom-card-" class="accordion-item new-card">
            <a href="javascript:void(0)" class="accordion-title plain">
                <div>
                    New card title
                    <button class="toggle" aria-label="Toggle">
                        <i class="icon-angle-down"></i>
                    </button>
                </div>
                <div>
                    <span class="delete--card">
                        <img draggable="false" role="img" class="emoji" alt="❌" src="https://s.w.org/images/core/emoji/14.0.0/svg/274c.svg">
                    </span>
                </div>
            </a>
            <div class="accordion-inner">
                <h3>Side A</h3>
                <p>Title of card</p>
                <input type="text" id="new_cc_title" name="new_cc_title" value="">
                <p>Card description</p>
                <textarea type="text" id="new_cc_description" name="new_cc_desc" value=""></textarea>
                <h3>Side B</h3>
                <div class="cc_field_image">
                    <a href="#" class="rudr-upload button button-primary">Upload image</a>
                    <a href="javascript:void(0)" class="rudr-remove" style="display: none;">Remove image</a>
                    <input type="hidden" name="rudr_img" value="">
                </div>
                <div class="save--card hidden">
                    <a href="javascript:void(0)">Save Card</a>
                </div>
            </div>
          </div>
        `);
      webozzaUploader();
      ccToggleNew();
      saveCard();
      duplicateSet();
      $(".custom-cards .new-card:last-child .delete--card").click(function () {
        $(this).parent().parent().parent().remove();
      });
      var newCardsOffset = $(".custom-cards .new-card:last-child").offset().top;
      $("html, body").animate(
        {
          scrollTop: newCardsOffset,
        },
        "fast"
      );
    });
  };

  /* Clicks on Edit Set Icon
   * ------------------------------------------------------- */
  let editSetBtn = () => {
    $(".btn-crud .cc-edit").click(function () {
      $(this).find("form").trigger("submit");
    });
  };

  /* UPDATE SET
   * ------------------------------------------------------- */
  var _updateSet = {
    title: "",
    status: "publish",
    content: "",
  };

  let fetchUpdate = async () => {
    const url = `/wp-json/wp/v2/portfolio_sets/${setId}`;
    let res = await fetch(url, {
      method: "PUT",
      body: JSON.stringify(_updateSet),
      headers: {
        "Content-type": "application/json; charset=UTF-8",
        "X-WP-Nonce": flashcardSettings.nonce,
      },
    });
    return await res.json();
  };

  let renderUpdate = async () => {
    let updateSet = await fetchUpdate();
  };

  let onceLoaded = async () => {
    let hostName = window.location.hostname;
    let pathName = window.location.pathname;
    let redirectTo = `https://${hostName}${pathName}?set=${setId}`;
    window.location.href = redirectTo;
  };

  let updateOnceLoaded = async () => {
    let hostName = window.location.hostname;
    let pathName = window.location.pathname;
    let redirectTo = `https://${hostName}${pathName}`;
    window.location.href = redirectTo;
  };

  /* UPDATE CUSTOM CARD
   * ------------------------------------------------------- */
  var _updateCard = {
    title: "",
    status: "publish",
    content: "",
    featured_media: "",
  };

  var cardId;

  let fetchUpdateCard = async () => {
    const url = `/wp-json/wp/v2/portfolio_flashcards/${cardId}`;
    let res = await fetch(url, {
      method: "PUT",
      body: JSON.stringify(_updateCard),
      headers: {
        "Content-type": "application/json; charset=UTF-8",
        "X-WP-Nonce": flashcardSettings.nonce,
      },
    });
    return await res.json();
  };

  let renderUpdateCard = async () => {
    let updateSet = await fetchUpdateCard();
  };

  let updateCard = () => {
    $(".update--card").each(async function () {
      let updateCardFuncs = async () => {
        cardId = $(this).parent().parent().attr("id").slice(12);
        _updateCard.title = $(this).parent().find("input").val();
        _updateCard.content = $(this).parent().find("textarea").val();
        _updateCard.featured_media = $(this)
          .parent()
          .find("input[name='rudr_img']")
          .val();
        $(this).parent().parent().find(".card---title").text(_updateCard.title);
      };
      await updateCardFuncs();
      renderUpdateCard();
    });
  };

  /* SAVE NEW CUSTOM CARD
   * ------------------------------------------------------- */
  var _saveCard = {
    title: "",
    status: "publish",
    content: "",
    meta: {
      parent_sets: "",
    },
    featured_media: "",
  };

  let fetchSaveCard = async () => {
    const url = `/wp-json/wp/v2/portfolio_flashcards/`;
    let res = await fetch(url, {
      method: "POST",
      body: JSON.stringify(_saveCard),
      headers: {
        "Content-type": "application/json; charset=UTF-8",
        "X-WP-Nonce": flashcardSettings.nonce,
      },
    });
    return await res.json();
  };

  let renderSaveCard = async () => {
    let updateSet = await fetchSaveCard();
  };

  let saveCard = () => {
    $(".custom-cards .new-card:last-child .save--card").click(
      async function () {
        let saveCardFuncs = async () => {
          _saveCard.title = $(this).parent().find("input").val();
          _saveCard.content = $(this).parent().find("textarea").val();
          _saveCard.meta.parent_sets = setId;
          _saveCard.featured_media = $(this)
            .parent()
            .find('input[name="rudr_img"]')
            .val();
        };
        await saveCardFuncs();
        renderSaveCard();
      }
    );
  };

  /* DELETE EXISTING CUSTOM CARD
   * ---------------------------------------------------------------------*/
  var deleteId;

  let prepareDelete = async () => {
    const url = `/wp-json/wp/v2/portfolio_flashcards/${deleteId}`;
    let res = await fetch(url, {
      method: "DELETE",
      headers: {
        "Content-type": "application/json; charset=UTF-8",
        "X-WP-Nonce": flashcardSettings.nonce,
      },
    });
    return await res.json();
  };

  let deleteCustomCards = async () => {
    let deleteCards = await prepareDelete();
  };

  let deleteCard = async () => {
    $(".delete--card").click(function () {
      deleteId = $(this).parent().parent().parent().attr("id").slice(12);
      deleteCustomCards();
      $(this).parent().parent().parent().remove();
    });
  };

  /* Check Post Meta
   * ------------------------------------------------------- */
  let fetchAllPosts = async () => {
    const url = `/wp-json/wp/v2/portfolio_sets`;
    let res = await fetch(url, {
      method: "GET",
    });
    return await res.json();
  };

  let checkAllPosts = async () => {
    let postData = await fetchAllPosts();
  };
  checkAllPosts();

  /* Delete set
   * ------------------------------------------------------- */
  let deleteSet = () => {
    $(".cc-delete").click(async function () {
      setToDelete = $(this);
      if (!confirm("Are you sure?")) {
        // do nothing....
      } else {
        setToDelete.parent().find(".delete-set-form").submit();
      }
    });
  };

  /* Save Presets
   * ------------------------------------------------------- */
  var _presetData = {
    title: "",
    content: "",
    meta: {
      selected_presets: [],
    },
  };

  let fetchSavedPresets = async () => {
    const url = `/wp-json/wp/v2/portfolio_sets/${setId}`;
    let res = await fetch(url, {
      method: "GET",
      headers: {
        "Content-type": "application/json; charset=UTF-8",
        "X-WP-Nonce": flashcardSettings.nonce,
      },
    });
    return await res.json();
  };

  let fetchPostPresets = async () => {
    const url = `/wp-json/wp/v2/portfolio_sets/${setId}`;
    let res = await fetch(url, {
      method: "PUT",
      headers: {
        "Content-type": "application/json; charset=UTF-8",
        "X-WP-Nonce": flashcardSettings.nonce,
      },
      body: JSON.stringify(_presetData),
    });

    return await res.json();
  };

  let renderPostPresets = async () => {
    let postPresets = await fetchPostPresets();
  };

  let postSelectedPresets = () => {
    $(".save-preset").click(async function () {
      // prepares the selection
      let selectedCat = $(this).parent().find("select").attr("id");
      let selectedPresets = $(this).parent().find("select").find(":selected");
      let selectedPresetValues = [];
      selectedPresets.each(function () {
        selectedPresetValues.push($(this).val());
      });

      // publish the selection
      _presetData.meta.selected_presets.push({
        cat: selectedCat,
        ids: selectedPresetValues,
      });

      // fix the title and content bug
      _presetData.title = $(".set-title input").val();
      _presetData.content = $(".set-description textarea").val();

      renderPostPresets();
    });
  };

  let renderSavedPresets = async () => {
    let savedPresets = await fetchSavedPresets();
    let selectedPresetRecords = savedPresets.meta.selected_presets;
    selectedPresetRecords.map((entries) => {
      if (entries.ids.length > 0) {
        $(`#${entries.cat}`).val(entries.ids).trigger("change");
      }
    });
    postSelectedPresets();
  };

  /* Update admin bar edit page name and link
  ---------------------------------------------------------- */
  let overrideAdminBarEdit = () => {
    let hostName = window.location.host;
    if ($("#wp-admin-bar-edit").length > 0) {
      $("#wp-admin-bar-root-default").append(`
        <li id="wp-admin-bar-edit-set">
          <a href="https://${hostName}/wp-admin/post.php?post=${setId}&action=edit" class="ab-item">Edit Set</a>
        </li>
      `);
      $("#wp-admin-bar-edit").remove();
    }
  };

  let updateSet = () => {
    $("#updateSet").click(async function () {
      // set the title and description
      _updateSet.title = $(".set-title input").val();
      _updateSet.content = $(".set-description textarea").val();
      // update card....
      $(".update--card").each(function () {
        cardId = $(this).parent().parent().attr("id").slice(12);
        _updateCard.title = $(this).parent().find("input").val();
        _updateCard.content = $(this).parent().find("textarea").val();
        _updateCard.featured_media = $(this)
          .parent()
          .find("input[name='rudr_img']")
          .val();
        $(this).parent().parent().find(".card---title").text(_updateCard.title);
        renderUpdateCard();
      });

      // save card ...
      $(".save--card").each(function () {
        _saveCard.title = $(this).parent().find("input").val();
        _saveCard.content = $(this).parent().find("textarea").val();
        _saveCard.meta.parent_sets = setId;
        _saveCard.featured_media = $(this)
          .parent()
          .find('input[name="rudr_img"]')
          .val();
        renderSaveCard();
      });

      // preset card...
      $(".save-preset").each(function () {
        let selectedCat = $(this).parent().find("select").attr("id");
        let selectedPresets = $(this).parent().find("select").find(":selected");
        let selectedPresetValues = [];
        selectedPresets.each(function () {
          selectedPresetValues.push($(this).val());
        });

        // publish the selection
        _presetData.meta.selected_presets.push({
          cat: selectedCat,
          ids: selectedPresetValues,
        });

        // fix the title and content bug
        _presetData.title = $(".set-title input").val();
        _presetData.content = $(".set-description textarea").val();
      });

      await renderPostPresets();
      await renderUpdate();
      await updateOnceLoaded();
    });
  };

  /* Duplicate Set
   * ------------------------------------------------------- */
  let duplicateSet = () => {
    $(".cc-duplicate").click(function () {
      $(this).parent().find(".duplicate-set-form").submit();
    });
  };

  /* Initialize Functions
   * ------------------------------------------------------- */
  $(window).on("load", function () {
    checkAllPosts();
    editSetBtn();
    // updateSet();
    updateCard();
    newCustomCard();
    deleteCard();
    webozzaUploader();
    deleteSet();
    duplicateSet();

    $(".preset-selection").on("select2:unselect", function (e) {
      $(this).clear();
    });

    $.fn.select2.amd.require(["select2/selection/search"], function (Search) {
      var oldRemoveChoice = Search.prototype.searchRemoveChoice;

      Search.prototype.searchRemoveChoice = function () {
        oldRemoveChoice.apply(this, arguments);
        this.$search.val("");
      };

      $(".preset-selection").select2({
        templateResult: formatState,
        templateSelection: formatState,
        width: "300px",
      });
    });

    function formatState(opt) {
      if (!opt.id) {
        return opt.text;
      }
      var optimage = $(opt.element).attr("data-img_url");
      if (!optimage) {
        return opt.text;
      } else {
        var $opt = $(
          '<span><img src="' +
            optimage +
            '" width="80px" height="80px" /> ' +
            opt.text +
            "</span>"
        );
        return $opt;
      }
    }

    if (window.location.href.indexOf("?edit-set") > -1) {
      overrideAdminBarEdit();
      renderSavedPresets();
    }
  });


  setTimeout(()=>{
    $(".btn-study").on('click',()=>{
      $('.card-item.active').removeClass('active');
      console.log('sadsdsadsadasd')
    })
  },1000)
 
  // ENDS
})(jQuery);
