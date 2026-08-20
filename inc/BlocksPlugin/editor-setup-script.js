/**
 * Shadcn AI setup entry — shown only when the Shadcn Blocks plugin is not
 * active (the plugin ships the real UI; this script is never enqueued
 * alongside it). Registers the same sparkle sidebar in the same spot, but
 * the modal's job is getting the plugin active: one click installs from
 * WordPress.org (when missing) and activates, for users allowed to manage
 * plugins; everyone else is pointed at the right page.
 */
(function () {
  "use strict";

  var el = wp.element.createElement;
  var Fragment = wp.element.Fragment;
  var useState = wp.element.useState;
  var __ = wp.i18n.__;
  var registerPlugin = wp.plugins.registerPlugin;

  var PluginSidebar =
    (wp.editor && wp.editor.PluginSidebar) || wp.editPost.PluginSidebar;
  var PluginSidebarMoreMenuItem =
    (wp.editor && wp.editor.PluginSidebarMoreMenuItem) ||
    wp.editPost.PluginSidebarMoreMenuItem;

  var Modal = wp.components.Modal;
  var Button = wp.components.Button;

  var PLUGIN_NAME = "shadcn-ai";

  // Same sparkle as the plugin's own UI, so the entry does not appear to
  // move or change identity once the plugin is active.
  var SPARKLE_ICON = el(
    "svg",
    {
      width: 24,
      height: 24,
      viewBox: "0 0 24 24",
      fill: "none",
      xmlns: "http://www.w3.org/2000/svg",
      "aria-hidden": "true",
      focusable: "false",
    },
    el("path", {
      fill: "currentColor",
      d: "M12 2l1.9 5.6L19.5 9.5l-5.6 1.9L12 17l-1.9-5.6L4.5 9.5l5.6-1.9L12 2zm7 11l.95 2.8 2.8.95-2.8.95L19 20.5l-.95-2.8-2.8-.95 2.8-.95L19 13zM5 14l.8 2.3 2.3.8-2.3.8L5 20.2l-.8-2.3-2.3-.8 2.3-.8L5 14z",
    })
  );

  // Localized by the theme: whether the plugin is installed (inactive) or
  // absent, whether this user can fix that, and where to send them if not.
  var SETUP = window.shadcnAiSetup || {};

  function SetupModal(props) {
    var onClose = props.onClose;

    // idle → working → done | error.
    var s = useState("idle");
    var phase = s[0], setPhase = s[1];
    s = useState("");
    var errorMessage = s[0], setErrorMessage = s[1];

    var installed = !!SETUP.installed;

    function runSetup() {
      setPhase("working");
      setErrorMessage("");
      var body = new window.FormData();
      body.append("action", "shadcn_ai_install_blocks");
      body.append("nonce", SETUP.nonce);
      window
        .fetch(SETUP.ajaxUrl, {
          method: "POST",
          credentials: "same-origin",
          body: body,
        })
        .then(function (response) {
          return response.json();
        })
        .then(function (result) {
          if (!result || !result.success) {
            setErrorMessage((result && result.data) || "");
            setPhase("error");
            return;
          }
          setPhase("done");
        })
        .catch(function () {
          setPhase("error");
        });
    }

    return el(
      Modal,
      {
        title: __("Shadcn AI", "shadcn"),
        onRequestClose: onClose,
        className: "shadcn-ai-setup-modal",
      },
      el(
        "p",
        null,
        phase === "done"
          ? __(
              "Shadcn Blocks is now active. Reload the editor to start composing.",
              "shadcn"
            )
          : SETUP.canSetup
            ? installed
              ? __(
                  "Shadcn AI needs the Shadcn Blocks plugin. It is installed but not active — one click below and you are ready.",
                  "shadcn"
                )
              : __(
                  "Shadcn AI needs the Shadcn Blocks plugin. One click below installs it from WordPress.org and activates it — no other setup is required.",
                  "shadcn"
                )
            : __(
                "Shadcn AI needs the Shadcn Blocks plugin. Ask a site administrator to install and activate it.",
                "shadcn"
              )
      ),
      phase === "error"
        ? el(
            "p",
            { className: "shadcn-ai-setup-modal__error" },
            errorMessage ||
              __(
                "That did not work — try again, or set the plugin up from the Plugins screen.",
                "shadcn"
              )
          )
        : null,
      el(
        "div",
        { className: "shadcn-ai-setup-modal__actions" },
        phase === "done"
          ? // A reload is genuinely required: the plugin's blocks were not
            // registered when this editor loaded, so generated sections
            // would arrive as missing blocks without it.
            el(
              Button,
              {
                variant: "primary",
                onClick: function () {
                  window.location.reload();
                },
              },
              __("Reload editor", "shadcn")
            )
          : SETUP.canSetup
            ? el(
                Button,
                {
                  variant: "primary",
                  isBusy: phase === "working",
                  disabled: phase === "working",
                  onClick: runSetup,
                },
                phase === "working"
                  ? installed
                    ? __("Activating…", "shadcn")
                    : __("Installing…", "shadcn")
                  : installed
                    ? __("Activate Shadcn Blocks", "shadcn")
                    : __("Install Shadcn Blocks", "shadcn")
              )
            : SETUP.fallbackUrl
              ? // Plugins screen when only activation is missing; the public
                // WordPress.org page when the plugin is absent (the admin
                // install screens would refuse this user).
                el(
                  Button,
                  {
                    variant: "primary",
                    href: SETUP.fallbackUrl,
                    target: SETUP.installed ? undefined : "_blank",
                  },
                  SETUP.installed
                    ? __("Open Plugins", "shadcn")
                    : __("View Shadcn Blocks plugin", "shadcn")
                )
              : null,
        el(
          Button,
          { variant: "tertiary", onClick: onClose },
          __("Close", "shadcn")
        )
      )
    );
  }

  function ShadcnAiSetupPanel() {
    var s = useState(false);
    var open = s[0], setOpen = s[1];

    return el(
      Fragment,
      null,
      el(
        PluginSidebarMoreMenuItem,
        { target: PLUGIN_NAME, icon: SPARKLE_ICON },
        __("Shadcn AI", "shadcn")
      ),
      el(
        PluginSidebar,
        {
          name: PLUGIN_NAME,
          icon: SPARKLE_ICON,
          title: __("Shadcn AI", "shadcn"),
        },
        el(
          "div",
          { className: "shadcn-ai-launcher" },
          el(
            "p",
            { className: "shadcn-ai-launcher__description" },
            __(
              "Describe a page and let AI compose it from Shadcn patterns — preview first, then insert.",
              "shadcn"
            )
          ),
          el(
            Button,
            {
              variant: "primary",
              className: "shadcn-ai-launcher__open",
              onClick: function () {
                setOpen(true);
              },
            },
            __("Open Shadcn AI", "shadcn")
          )
        ),
        open
          ? el(SetupModal, {
              onClose: function () {
                setOpen(false);
              },
            })
          : null
      )
    );
  }

  registerPlugin(PLUGIN_NAME, {
    render: ShadcnAiSetupPanel,
    icon: SPARKLE_ICON,
  });
})();
