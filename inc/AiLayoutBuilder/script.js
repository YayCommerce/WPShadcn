/**
 * AI Layout Builder — editor sidebar panel.
 *
 * Prompt in, calls the /shadcn/v1/ai-layout/generate REST route, previews
 * the returned blocks read-only, and inserts them into the post being
 * edited on confirmation.
 */
(function () {
  "use strict";

  var el = wp.element.createElement;
  var Fragment = wp.element.Fragment;
  var useState = wp.element.useState;
  var __ = wp.i18n.__;
  var registerPlugin = wp.plugins.registerPlugin;

  // WP moved PluginSidebar from wp.editPost to wp.editor over time; support both.
  var PluginSidebar =
    (wp.editor && wp.editor.PluginSidebar) || wp.editPost.PluginSidebar;
  var PluginSidebarMoreMenuItem =
    (wp.editor && wp.editor.PluginSidebarMoreMenuItem) ||
    wp.editPost.PluginSidebarMoreMenuItem;

  var PanelBody = wp.components.PanelBody;
  var TextareaControl = wp.components.TextareaControl;
  var Button = wp.components.Button;
  var Notice = wp.components.Notice;
  var Spinner = wp.components.Spinner;

  var BlockPreview =
    wp.blockEditor.BlockPreview || wp.blockEditor.__experimentalBlockPreview;

  var PLUGIN_NAME = "shadcn-ai-layout-builder";

  function AiLayoutBuilderPanel() {
    var promptState = useState("");
    var prompt = promptState[0];
    var setPrompt = promptState[1];

    var loadingState = useState(false);
    var loading = loadingState[0];
    var setLoading = loadingState[1];

    var errorState = useState("");
    var error = errorState[0];
    var setError = errorState[1];

    var warningsState = useState([]);
    var warnings = warningsState[0];
    var setWarnings = warningsState[1];

    var previewState = useState(null);
    var previewBlocks = previewState[0];
    var setPreviewBlocks = previewState[1];

    function resetPanel() {
      setPrompt("");
      setPreviewBlocks(null);
      setWarnings([]);
      setError("");
    }

    function handleGenerate() {
      if (loading || !prompt) {
        return;
      }

      setLoading(true);
      setError("");
      setWarnings([]);
      setPreviewBlocks(null);

      wp.apiFetch({
        path: "/shadcn/v1/ai-layout/generate",
        method: "POST",
        data: { prompt: prompt },
      })
        .then(function (response) {
          var blocks = wp.blocks.parse(
            (response && response.markup) || ""
          );
          setPreviewBlocks(blocks);
          setWarnings((response && response.warnings) || []);
          setLoading(false);
        })
        .catch(function (err) {
          var message =
            (err && err.message) ||
            __(
              "Something went wrong generating the layout. Please try again.",
              "shadcn"
            );

          // On a total-plan-failure (422), the server includes per-entry
          // rejection reasons (unknown slug, malformed shape, etc.) in
          // err.data.warnings — surface them, since "no valid layout" alone
          // doesn't say WHY every entry was rejected.
          if (err && err.data && err.data.warnings && err.data.warnings.length) {
            message += " " + err.data.warnings.join(" ");
          }

          setError(message);
          setLoading(false);
        });
    }

    function handleInsert() {
      if (!previewBlocks || !previewBlocks.length) {
        return;
      }

      wp.data.dispatch("core/block-editor").insertBlocks(previewBlocks);
      resetPanel();
    }

    return el(
      Fragment,
      null,
      el(
        PluginSidebarMoreMenuItem,
        { target: PLUGIN_NAME, icon: "art" },
        __("AI Layout Builder", "shadcn")
      ),
      el(
        PluginSidebar,
        {
          name: PLUGIN_NAME,
          icon: "art",
          title: __("AI Layout Builder", "shadcn"),
        },
        el(
          PanelBody,
          null,
          el(TextareaControl, {
            label: __("Describe the layout you want", "shadcn"),
            help: __(
              "e.g. “landing page with a hero, three features, and a footer”",
              "shadcn"
            ),
            value: prompt,
            onChange: setPrompt,
            disabled: loading,
            rows: 4,
            __nextHasNoMarginBottom: true,
          }),
          el(
            Button,
            {
              variant: "primary",
              onClick: handleGenerate,
              disabled: loading || !prompt,
              isBusy: loading,
              className: "shadcn-ai-layout-builder__generate",
            },
            loading ? __("Generating…", "shadcn") : __("Generate", "shadcn")
          ),
          loading &&
            el(
              "div",
              { className: "shadcn-ai-layout-builder__loading" },
              el(Spinner, null)
            ),
          error &&
            el(
              Notice,
              {
                status: "error",
                isDismissible: false,
                className: "shadcn-ai-layout-builder__notice",
              },
              error
            ),
          !error &&
            warnings.length > 0 &&
            el(
              Notice,
              {
                status: "warning",
                isDismissible: false,
                className: "shadcn-ai-layout-builder__notice",
              },
              warnings.join(" ")
            ),
          previewBlocks &&
            el(
              "div",
              { className: "shadcn-ai-layout-builder__preview" },
              BlockPreview
                ? el(BlockPreview, {
                    blocks: previewBlocks,
                    viewportWidth: 800,
                  })
                : el(
                    "p",
                    null,
                    __(
                      "Preview isn't available in this WordPress version, but you can still insert the layout below.",
                      "shadcn"
                    )
                  )
            ),
          previewBlocks &&
            el(
              "div",
              { className: "shadcn-ai-layout-builder__actions" },
              el(
                Button,
                { variant: "primary", onClick: handleInsert },
                __("Insert", "shadcn")
              ),
              el(
                Button,
                { variant: "tertiary", onClick: resetPanel },
                __("Discard", "shadcn")
              )
            )
        )
      )
    );
  }

  registerPlugin(PLUGIN_NAME, {
    render: AiLayoutBuilderPanel,
    icon: "art",
  });
})();
