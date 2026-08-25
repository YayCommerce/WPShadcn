/**
 * Layout picker for WooCommerce blocks that cannot be rearranged by hand.
 *
 * Adds a "Change layout" toolbar button to every block declared as a picker
 * family in Caller.php. The button opens a modal of layouts; picking one applies
 * the layout pattern's root attributes, replaces the block's inner blocks and
 * sets the layout class that drives the family's stylesheet.
 *
 * Layout data is injected from PHP as `window.shadcnLayoutPickers` because the
 * editor's getBlockPatterns() selector omits patterns registered with
 * `Inserter: no`.
 *
 * The modal is deliberately NOT rendered from inside the block's edit
 * component. In the Site Editor the canvas is an iframe, so a modal rendered
 * there portals into the iframe's document: its backdrop only covers the
 * canvas, and its focus trap fights the editor's own focus management across
 * the frame boundary, which locks the editor up. Instead the toolbar button
 * publishes to a small module-level store, and a plugin registered on the
 * editor root - outside the iframe - renders the modal.
 */
(function () {
  "use strict";

  var config = window.shadcnLayoutPickers;

  if (!config || !config.families || !config.families.length) {
    return;
  }

  var el = wp.element.createElement;
  var Fragment = wp.element.Fragment;
  var useState = wp.element.useState;
  var useEffect = wp.element.useEffect;
  var __ = wp.i18n.__;
  var sprintf = wp.i18n.sprintf;
  var BlockControls = wp.blockEditor.BlockControls;
  var ToolbarGroup = wp.components.ToolbarGroup;
  var ToolbarButton = wp.components.ToolbarButton;
  var Modal = wp.components.Modal;
  var ConfirmDialog = wp.components.__experimentalConfirmDialog;

  // Block name -> family, so the toolbar filter is one lookup per render
  // instead of a scan over every family.
  var FAMILIES_BY_BLOCK = {};

  config.families.forEach(function (family) {
    FAMILIES_BY_BLOCK[family.targetBlock] = family;
  });

  /* ----------------------------------------------------------------------
   * Picker store
   *
   * Bridges the toolbar button (rendered inside the canvas iframe) and the
   * modal (rendered on the editor root). Deliberately tiny - a full data
   * store would be more machinery than one family and a client id need.
   * -------------------------------------------------------------------- */

  var state = { family: null, clientId: null };
  var listeners = [];

  function notify() {
    listeners.forEach(function (listener) {
      listener();
    });
  }

  function subscribe(listener) {
    listeners.push(listener);

    return function () {
      listeners = listeners.filter(function (item) {
        return item !== listener;
      });
    };
  }

  function openPicker(family, clientId) {
    state = { family: family, clientId: clientId };
    notify();
  }

  function closePicker() {
    state = { family: null, clientId: null };
    notify();
  }

  /* ----------------------------------------------------------------------
   * Schematic previews
   *
   * Real block previews are impossible here: BlockPreview renders nothing for
   * these blocks because they resolve their contents from the Store API at
   * runtime. So each family draws a skeleton out of WooCommerce's own class
   * names and its real DOM shape, and lets the family stylesheet - which the
   * editor loads too - arrange it. That keeps previews in sync with the CSS
   * automatically, where a static screenshot would drift every time a layout
   * rule changed.
   * -------------------------------------------------------------------- */

  function line(key, modifier) {
    return el("span", {
      key: key,
      className: "shadcn-layout-preview__line" + (modifier ? " " + modifier : ""),
    });
  }

  /**
   * Mini-Cart skeleton.
   *
   * Mirrors WooCommerce's markup down to the table, tbody, tr and td, not just
   * the class names. The layout rules target that structure - the two-column
   * grid lives on <tbody>, because the rows are <tr> children of it - so a
   * div-based stand-in would silently stop matching and show a layout the
   * drawer never renders.
   *
   * Three rows is enough to read the arrangement inside a cropped thumbnail: a
   * two-column layout still shows its second column, a list still shows that it
   * stacks.
   */
  function renderMiniCartPreview(layout) {
    var rows = [0, 1, 2].map(function (index) {
      return el(
        "tr",
        { className: "wc-block-cart-items__row", key: index },
        el("td", { className: "wc-block-cart-item__image" }, el("span", null)),
        el(
          "td",
          { className: "wc-block-cart-item__product" },
          line("a"),
          line("b", "is-short")
        ),
        el("td", { className: "wc-block-cart-item__total" }, el("span", null))
      );
    });

    return el(
      "div",
      { className: layout.className },
      el("div", { className: "wc-block-mini-cart__title" }),
      el("table", { className: "wc-block-cart-items" }, el("tbody", null, rows)),
      el("div", { className: "shadcn-layout-preview__bar" })
    );
  }

  /**
   * Checkout skeleton.
   *
   * Reproduces the two elements the checkout layouts actually address: the
   * sidebar-layout grid, and the main/sidebar pair inside it. The layout class
   * sits on the outer wrapper exactly like it does on the front end, so a
   * layout that flips the columns or collapses them flips them here too.
   */
  function renderCheckoutPreview(layout) {
    var steps = [0, 1, 2].map(function (index) {
      return el(
        "div",
        { className: "wc-block-components-checkout-step", key: index },
        el("span", { className: "shadcn-layout-preview__bar is-title" }),
        line("a"),
        line("b", "is-short")
      );
    });

    return el(
      "div",
      { className: "wp-block-woocommerce-checkout wc-block-checkout " + layout.className },
      el(
        "div",
        { className: "wc-block-components-sidebar-layout wc-block-checkout" },
        el(
          "div",
          { className: "wc-block-components-main wc-block-checkout__main" },
          steps
        ),
        el(
          "div",
          { className: "wc-block-components-sidebar wc-block-checkout__sidebar" },
          el(
            "div",
            { className: "wp-block-woocommerce-checkout-order-summary-block" },
            el("span", { className: "shadcn-layout-preview__bar is-title" }),
            line("a"),
            line("b", "is-short"),
            line("c")
          )
        )
      )
    );
  }

  var PREVIEWS = {
    "mini-cart": renderMiniCartPreview,
    checkout: renderCheckoutPreview,
  };

  function renderPreview(family, layout) {
    var draw = PREVIEWS[family.preview];

    if (!draw) {
      return null;
    }

    return el(
      "div",
      {
        className:
          "shadcn-layout-preview shadcn-layout-preview--" + family.preview,
        "aria-hidden": "true",
      },
      draw(layout)
    );
  }

  /* ----------------------------------------------------------------------
   * Layout helpers
   * -------------------------------------------------------------------- */

  /**
   * Find the layout currently applied to a block, based on its class name.
   */
  function findActiveLayout(family, className) {
    if (!className) {
      return null;
    }

    var classes = String(className).split(/\s+/);

    for (var i = 0; i < family.layouts.length; i++) {
      if (classes.indexOf(family.layouts[i].className) !== -1) {
        return family.layouts[i];
      }
    }

    return null;
  }

  /**
   * Parse a layout pattern down to its root block.
   */
  function parseLayout(family, layout) {
    var parsed = wp.blocks.parse(layout.content);

    if (!parsed.length || parsed[0].name !== family.targetBlock) {
      return null;
    }

    return parsed[0];
  }

  function getLayoutInnerBlocks(family, layout) {
    var root = parseLayout(family, layout);

    return root ? root.innerBlocks : [];
  }

  function serializeBlocks(blocks) {
    return wp.blocks.serialize(blocks);
  }

  /**
   * Decide whether applying a layout would throw away edits made by hand.
   *
   * Nothing is lost when the block already matches either side of the swap: if
   * it matches the layout it claims to be, or if it already matches the layout
   * about to be applied. The checkout layouts all share one block tree - they
   * differ only in attributes and CSS - so that second test is what keeps
   * switching between them free of pointless warnings.
   */
  function willLoseEdits(family, clientId, className, target) {
    try {
      var current = serializeBlocks(
        wp.data.select("core/block-editor").getBlocks(clientId)
      );

      if (current === serializeBlocks(getLayoutInnerBlocks(family, target))) {
        return false;
      }

      var active = findActiveLayout(family, className);

      if (!active) {
        return true;
      }

      return current !== serializeBlocks(getLayoutInnerBlocks(family, active));
    } catch (e) {
      return true;
    }
  }

  /**
   * Apply a layout to a block: its pattern's root attributes, its inner blocks
   * and its layout class.
   */
  function applyLayout(family, clientId, layout) {
    var root = parseLayout(family, layout);

    if (!root) {
      window.console &&
        window.console.warn("[shadcn] Unusable layout markup:", layout.key);
      return;
    }

    var editor = wp.data.select("core/block-editor");
    var block = editor.getBlock(clientId);

    if (!block) {
      return;
    }

    // Drop any previous layout class before adding the new one, so switching
    // layouts repeatedly does not stack stale classes on the block.
    var kept = String(block.attributes.className || "")
      .split(/\s+/)
      .filter(function (name) {
        if (!name) {
          return false;
        }
        return !family.layouts.some(function (item) {
          return item.className === name;
        });
      });

    kept.push(layout.className);

    // Only the attributes the pattern states outright, supplied by PHP. A
    // layout can therefore carry a setting that CSS cannot express - the
    // checkout's numbered form steps, for one - without touching the settings
    // it says nothing about. className is computed above instead, so classes
    // the user added by hand survive the swap.
    var attributes = Object.assign({}, layout.attributes, {
      className: kept.join(" "),
    });

    var dispatch = wp.data.dispatch("core/block-editor");

    // Skip the swap when the tree is already the one the layout wants. Every
    // checkout layout ships the same tree - they differ in attributes and CSS
    // alone - and replacing it would discard settings made on the inner blocks
    // for no gain.
    var isSameTree =
      serializeBlocks(editor.getBlocks(clientId)) ===
      serializeBlocks(root.innerBlocks);

    if (root.innerBlocks.length && !isSameTree) {
      dispatch.replaceInnerBlocks(clientId, root.innerBlocks, false);
    }

    dispatch.updateBlockAttributes(clientId, attributes);
  }

  /* ----------------------------------------------------------------------
   * Toolbar button - rendered inside the block, i.e. inside the canvas iframe
   * -------------------------------------------------------------------- */

  var withLayoutPicker = wp.compose.createHigherOrderComponent(function (
    BlockEdit
  ) {
    return function (props) {
      var family = FAMILIES_BY_BLOCK[props.name];

      if (!family) {
        return el(BlockEdit, props);
      }

      return el(
        Fragment,
        null,
        el(BlockEdit, props),
        props.isSelected &&
          el(
            BlockControls,
            { group: "other" },
            el(
              ToolbarGroup,
              null,
              el(ToolbarButton, {
                icon: "layout",
                label: __("Change layout", "shadcn"),
                onClick: function () {
                  openPicker(family, props.clientId);
                },
              })
            )
          )
      );
    };
  },
  "withShadcnLayoutPicker");

  wp.hooks.addFilter(
    "editor.BlockEdit",
    "shadcn/layout-picker",
    withLayoutPicker
  );

  /* ----------------------------------------------------------------------
   * Modal - rendered on the editor root, outside the canvas iframe
   * -------------------------------------------------------------------- */

  function LayoutPickerRoot() {
    var snapshotState = useState(state);
    var snapshot = snapshotState[0];
    var setSnapshot = snapshotState[1];

    // Layout waiting on the user to confirm an overwrite, or null when the
    // swap needs no confirmation.
    var pendingState = useState(null);
    var pendingLayout = pendingState[0];
    var setPendingLayout = pendingState[1];

    useEffect(function () {
      return subscribe(function () {
        setSnapshot(state);
      });
    }, []);

    var family = snapshot.family;

    if (!family) {
      return null;
    }

    var block = wp.data.select("core/block-editor").getBlock(snapshot.clientId);
    var activeClassName = block ? block.attributes.className : "";

    function commit(layout) {
      applyLayout(family, snapshot.clientId, layout);
      setPendingLayout(null);
      closePicker();
    }

    function handleSelect(layout) {
      if (willLoseEdits(family, snapshot.clientId, activeClassName, layout)) {
        setPendingLayout(layout);
        return;
      }

      commit(layout);
    }

    // Only ever mount one modal at a time. Rendering the confirmation on top
    // of the open picker moves focus out of the picker, which fires its
    // onRequestClose and tears both of them down before the user can answer.
    if (pendingLayout) {
      return el(
        ConfirmDialog,
        {
          isOpen: true,
          confirmButtonText: __("Replace", "shadcn"),
          onConfirm: function () {
            commit(pendingLayout);
          },
          onCancel: function () {
            setPendingLayout(null);
          },
        },
        sprintf(family.confirm, pendingLayout.title)
      );
    }

    return el(
      Modal,
      {
        title: family.modalTitle,
        onRequestClose: function () {
          setPendingLayout(null);
          closePicker();
        },
        className: "shadcn-layout-modal",
      },
      el("p", { className: "shadcn-layout-modal__intro" }, family.intro),
      el(
        "div",
        { className: "shadcn-layout-modal__grid" },
        family.layouts.map(function (layout) {
          var isActive =
            String(activeClassName || "")
              .split(/\s+/)
              .indexOf(layout.className) !== -1;

          return el(
            "button",
            {
              type: "button",
              key: layout.key,
              className: "shadcn-layout-card" + (isActive ? " is-active" : ""),
              "aria-pressed": isActive,
              onClick: function () {
                handleSelect(layout);
              },
            },
            renderPreview(family, layout),
            el("span", { className: "shadcn-layout-card__title" }, layout.title)
          );
        })
      )
    );
  }

  wp.plugins.registerPlugin("shadcn-layout-picker", {
    render: LayoutPickerRoot,
  });
})();
