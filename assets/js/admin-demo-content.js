/**
 * Controla las acciones de los botones mediante Fetch API en vez de jQuery.
 */

document.addEventListener("DOMContentLoaded", function () {
  var btnCreatePages = document.getElementById("cdc-create-pages");
  var btnResetPages = document.getElementById("cdc-reset-pages");
  var btnCreatePosts = document.getElementById("cdc-create-posts");
  var btnResetPosts = document.getElementById("cdc-reset-posts");
  var btnDeletePosts = document.getElementById("cdc-delete-posts");
  var inputPostCount = document.getElementById("cdc-post-count");
  var btnCreateMenus = document.getElementById("cdc-create-menus");
  var btnResetMenus = document.getElementById("cdc-reset-menus");

  /**
   * Helper para realizar llamadas AJAX a admin-ajax.php
   *
   * @param {string} action Nombre de la acción AJAX (wp_ajax_{action}).
   * @param {Object} data   Datos adicionales a enviar ({ key: value }).
   * @return {Promise}      Promesa con la respuesta JSON.
   */
  function cdcFetchAction(action, data = {}) {
    // Agregar nonce y acción
    data.nonce = cdcAjax.nonce;
    data.action = action;

    var formData = new FormData();
    for (var key in data) {
      if (data.hasOwnProperty(key)) {
        formData.append(key, data[key]);
      }
    }

    return fetch(cdcAjax.ajax_url, {
      method: "POST",
      credentials: "same-origin",
      body: formData,
    }).then(function (response) {
      return response.json();
    });
  }

  // SECCIÓN 1: PÁGINAS
  if (btnCreatePages) {
    btnCreatePages.addEventListener("click", function (e) {
      e.preventDefault();

      if (!confirm(cdcAjax.confirmMsg)) {
        return;
      }

      // Recopilar todos los datos de la tabla
      var rows = document.querySelectorAll(
        ".cdc-table tbody tr[data-default-slug]"
      );
      var pagesData = [];

      rows.forEach(function (row) {
        var defaultSlug = row.getAttribute("data-default-slug");
        var titleInput = row.querySelector(".cdc-page-title");
        var slugInput = row.querySelector(".cdc-page-slug");

        var title = titleInput ? titleInput.value.trim() : "";
        var slug = slugInput ? slugInput.value.trim() : "";

        if (title !== "" && slug !== "") {
          pagesData.push({
            default_slug: defaultSlug,
            title: title,
            slug: slug,
          });
        }
      });

      if (pagesData.length === 0) {
        alert(cdcAjax.noPagesDataMsg || "No hay datos de páginas para crear.");
        return;
      }

      btnCreatePages.disabled = true;
      btnCreatePages.textContent = cdcAjax.creatingPagesText || "Creando…";

      cdcFetchAction("cdc_create_pages", { pages: JSON.stringify(pagesData) })
        .then(function (data) {
          if (data.success) {
            alert(data.data.message);
            window.location.reload();
          } else {
            alert(data.data.message || "Error al crear las páginas.");
            btnCreatePages.disabled = false;
            btnCreatePages.textContent = cdcAjax.createPagesText;
          }
        })
        .catch(function () {
          alert("Error en la petición AJAX.");
          btnCreatePages.disabled = false;
          btnCreatePages.textContent = cdcAjax.createPagesText;
        });
    });
  }

  if (btnResetPages) {
    btnResetPages.addEventListener("click", function (e) {
      e.preventDefault();

      if (!confirm(cdcAjax.confirmMsg)) {
        return;
      }

      btnResetPages.disabled = true;
      btnResetPages.textContent = cdcAjax.resettingPagesText || "Reiniciando…";

      cdcFetchAction("cdc_reset_pages_flag")
        .then(function (data) {
          if (data.success) {
            alert(data.data.message);
            window.location.reload();
          } else {
            alert(data.data.message || "Error al reiniciar páginas.");
            btnResetPages.disabled = false;
            btnResetPages.textContent = cdcAjax.resetPagesText;
          }
        })
        .catch(function () {
          alert("Error en la petición AJAX.");
          btnResetPages.disabled = false;
          btnResetPages.textContent = cdcAjax.resetPagesText;
        });
    });
  }

  // SECCIÓN 2: ENTRADAS

  if (btnCreatePosts) {
    btnCreatePosts.addEventListener("click", function (e) {
      e.preventDefault();

      if (!confirm(cdcAjax.confirmMsg)) {
        return;
      }

      // Leer la cantidad indicada (por defecto 3)
      var count = parseInt(inputPostCount.value, 10);
      if (isNaN(count) || count < 1) {
        alert(
          cdcAjax.noPostsCountMsg ||
            "Por favor indica un número válido de entradas."
        );
        return;
      }

      btnCreatePosts.disabled = true;
      btnCreatePosts.textContent = cdcAjax.creatingPostsText || "Creando…";

      cdcFetchAction("cdc_create_all_posts", { count: count })
        .then(function (data) {
          if (data.success) {
            alert(data.data.message);
            window.location.reload();
          } else {
            alert(data.data.message || "Error al crear las entradas.");
            btnCreatePosts.disabled = false;
            btnCreatePosts.textContent = cdcAjax.createPostsText;
          }
        })
        .catch(function () {
          alert("Error en la petición AJAX.");
          btnCreatePosts.disabled = false;
          btnCreatePosts.textContent = cdcAjax.createPostsText;
        });
    });
  }

  if (btnResetPosts) {
    btnResetPosts.addEventListener("click", function (e) {
      e.preventDefault();

      if (!confirm(cdcAjax.confirmMsg)) {
        return;
      }

      btnResetPosts.disabled = true;
      btnResetPosts.textContent = cdcAjax.resettingPostsText || "Reiniciando…";

      cdcFetchAction("cdc_reset_posts_flag")
        .then(function (data) {
          if (data.success) {
            alert(data.data.message);
            window.location.reload();
          } else {
            alert(data.data.message || "Error al reiniciar entradas.");
            btnResetPosts.disabled = false;
            btnResetPosts.textContent = cdcAjax.resetPostsText;
          }
        })
        .catch(function () {
          alert("Error en la petición AJAX.");
          btnResetPosts.disabled = false;
          btnResetPosts.textContent = cdcAjax.resetPostsText;
        });
    });
  }

  if (btnDeletePosts) {
    btnDeletePosts.addEventListener("click", function (e) {
      e.preventDefault();

      if (!confirm(cdcAjax.confirmMsg)) {
        return;
      }

      btnDeletePosts.disabled = true;
      btnDeletePosts.textContent = cdcAjax.deletingPostsText || "Eliminando…";

      cdcFetchAction("cdc_delete_all_posts")
        .then(function (data) {
          if (data.success) {
            alert(data.data.message);
            window.location.reload();
          } else {
            alert(data.data.message || "Error al eliminar las entradas.");
            btnDeletePosts.disabled = false;
            btnDeletePosts.textContent = cdcAjax.deletePostsText;
          }
        })
        .catch(function () {
          alert("Error en la petición AJAX.");
          btnDeletePosts.disabled = false;
          btnDeletePosts.textContent = cdcAjax.deletePostsText;
        });
    });
  }

  // SECCIÓN 3: MENÚS

  if (btnCreateMenus) {
    btnCreateMenus.addEventListener("click", function (e) {
      e.preventDefault();

      if (!confirm(cdcAjax.confirmMsg)) {
        return;
      }

      btnCreateMenus.disabled = true;
      btnCreateMenus.textContent = cdcAjax.creatingMenusText || "Creando…";

      cdcFetchAction("cdc_create_demo_menus")
        .then(function (data) {
          if (data.success) {
            alert(data.data.message);
            window.location.reload();
          } else {
            alert(data.data.message || "Error al crear menús.");
            btnCreateMenus.disabled = false;
            btnCreateMenus.textContent = cdcAjax.createMenusText;
          }
        })
        .catch(function () {
          alert("Error en la petición AJAX.");
          btnCreateMenus.disabled = false;
          btnCreateMenus.textContent = cdcAjax.createMenusText;
        });
    });
  }

  if (btnResetMenus) {
    btnResetMenus.addEventListener("click", function (e) {
      e.preventDefault();

      if (!confirm(cdcAjax.confirmMsg)) {
        return;
      }

      btnResetMenus.disabled = true;
      btnResetMenus.textContent = cdcAjax.resettingMenusText || "Reiniciando…";

      cdcFetchAction("cdc_reset_demo_menus_flag")
        .then(function (data) {
          if (data.success) {
            alert(data.data.message);
            window.location.reload();
          } else {
            alert(data.data.message || "Error al reiniciar menús.");
            btnResetMenus.disabled = false;
            btnResetMenus.textContent = cdcAjax.resetMenusText;
          }
        })
        .catch(function () {
          alert("Error en la petición AJAX.");
          btnResetMenus.disabled = false;
          btnResetMenus.textContent = cdcAjax.resetMenusText;
        });
    });
  }
});
