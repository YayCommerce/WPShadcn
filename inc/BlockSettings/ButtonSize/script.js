function isButtonBlock(name) {
  return ["core/button"].includes(name);
}

function isRestrictedButtonBlock(attributes) {
  return false;
}

/** Register attribute */
function addButtonSizeAttributes(settings) {
  if (typeof settings.attributes === "undefined") {
    return settings;
  }
  if (!isButtonBlock(settings.name)) {
    return settings;
  }
  settings.attributes = Object.assign(settings.attributes, {
    size: {
      type: "string",
      default: "",
    },
  });

  return settings;
}

wp.hooks.addFilter(
  "blocks.registerBlockType",
  "shadcn-blocks/button-size-attribute",
  addButtonSizeAttributes
);

/** Display controls */

const ButtonSizeControls = wp.compose.createHigherOrderComponent(
  (BlockEdit) => {
    return (props) => {
      const { Fragment } = wp.element;
      const {
        __experimentalToolsPanel: ToolsPanel,
        __experimentalToolsPanelItem: ToolPanelItem,
        __experimentalToggleGroupControl: ToggleGroupControl,
        __experimentalToggleGroupControlOption: ToggleGroupControlOption,
      } = wp.components;
      const { InspectorControls } = wp.blockEditor;
      const { isSelected, attributes, setAttributes, name } = props;
      const canAddSettings =
        isButtonBlock(name) && !isRestrictedButtonBlock(attributes);

      return React.createElement(
        Fragment,
        null,
        React.createElement(BlockEdit, props),
        isSelected &&
          canAddSettings &&
          React.createElement(
            InspectorControls,
            {},
            React.createElement(
              ToolsPanel,
              {
                label: wp.i18n.__("Size settings", "shadcn"),
                resetAll: () =>
                  setAttributes({
                    size: undefined,
                  }),
              },
              React.createElement(
                ToolPanelItem,
                {
                  label: wp.i18n.__("Size", "shadcn"),
                  isShownByDefault: true,
                  hasValue: () => !!attributes.size,
                  onDeselect: () => setAttributes({ size: undefined }),
                  __nextHasNoMarginBottom: true,
                },
                React.createElement(
                  ToggleGroupControl,
                  {
                    value: attributes.size,
                    isBlock: true,
                    __next40pxDefaultSize: true,
                    __nextHasNoMarginBottom: true,
                    onChange: (value) => {
                      setAttributes({
                        size: value,
                      });
                    },
                  },
                  React.createElement(ToggleGroupControlOption, {
                    value: "sm",
                    label: wp.i18n.__("Small", "shadcn"),
                  }),
                  React.createElement(ToggleGroupControlOption, {
                    value: "md",
                    label: wp.i18n.__("Medium", "shadcn"),
                  }),
                  React.createElement(ToggleGroupControlOption, {
                    value: "lg",
                    label: wp.i18n.__("Large", "shadcn"),
                  })
                )
              )
            )
          )
      );
    };
  },
  "FeaturedImagePlaceholderControls"
);

wp.hooks.addFilter(
  "editor.BlockEdit",
  "shadcn-blocks/button-size-controls",
  ButtonSizeControls,
  1
);

const addButtonSizeStyleToBlock = wp.compose.createHigherOrderComponent(
  (BlockListBlock) => {
    return (props) => {
      const { attributes, name } = props;

      const extraWrapperProps = props.wrapperProps ?? {};

      if (
        !isRestrictedButtonBlock(attributes) &&
        isButtonBlock(name) &&
        attributes.size != null &&
        attributes.size !== ""
      ) {
        extraWrapperProps.className =
          (props.wrapperProps.className ?? "") + ` is-size-${attributes.size}`;
      }

      return React.createElement(BlockListBlock, props, extraWrapperProps);
    };
  },
  "addButtonSizeStyleToBlock"
);

wp.hooks.addFilter(
  "editor.BlockListBlock",
  "shadcn/button-size-style",
  addButtonSizeStyleToBlock,
  1
);

/**
 * Save function
 */

function addButtonSizeProps(props, blockType, attributes) {
  if (
    !isButtonBlock(blockType.name ?? "") ||
    isRestrictedButtonBlock(attributes)
  ) {
    return props;
  }

  if (attributes.size != null && attributes.size !== "") {
    Object.assign(props, {
      className: (props.className ?? "") + ` is-size-${attributes.size}`,
    });
  }

  return props;
}

wp.hooks.addFilter(
  "blocks.getSaveContent.extraProps",
  "shadcn-blocks/button-size-props",
  addButtonSizeProps
);
