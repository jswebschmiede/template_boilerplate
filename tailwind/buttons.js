const plugin = require("tailwindcss/plugin");

const buttons = plugin(function ({ addUtilities, theme }) {
  const buttonUtilities = {
    ".btn": {
      "background-color": "transparent",
      display: "inline-flex",
      border: "1px solid transparent",
      "padding-left": "1rem",
      "padding-right": "1rem",
      height: "2.882rem",
      "max-height": "2.882rem",
      "text-align": "center",
      "line-height": "1.5",
      "vertical-align": "middle",
      "text-decoration": "none",
      "align-items": "center",
      "justify-content": "center",
      "flex-shrink": "0",
      "flex-wrap": "wrap",
      "font-size": "inherit",
      cursor: "pointer",
      "border-radius": "0.5rem",
      "transition-duration": "0.25s",
      "transition-timing-function": "cubic-bezier(0,0,.2,1)",
      "transition-property":
        "color,background-color,border-color,opacity,box-shadow,transform",
      "user-select": "none",
      "will-change":
        "color,background-color,border-color,opacity,box-shadow,transform",
      "box-shadow":
        "var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000), 0 1px 2px 0 rgb(0 0 0 / .05)",
      animation: "button-pop 0.25s ease-out",
      "&:hover": {
        "text-decoration": "none",
      },
      "&:active:hover, &:active:focus": {
        animation: "button-pop 0s ease-out",
        transform: "scale(.97)",
      },
    },
    ".btn-sm": {
      padding: "0.25rem 0.5rem",
      "font-size": "0.875rem",
    },
    ".btn-primary": {
      color: theme("colors.white"),
      "background-color": theme("colors.primary.500"),
      "&:hover": {
        "background-color": theme("colors.primary.600"),
        color: theme("colors.white"),
      },
    },
    ".btn-secondary": {
      color: theme("colors.white"),
      "background-color": theme("colors.primary.700"),
      "&:hover": {
        "background-color": theme("colors.primary.800"),
        color: theme("colors.white"),
      },
    },
    ".btn-outline": {
      color: theme("colors.black"),
      "border-color": theme("colors.primary.500"),
      "&:hover": {
        "background-color": theme("colors.primary.600"),
        "border-color": theme("colors.primary.600"),
        color: theme("colors.white"),
      },
    },
  };

  addUtilities(buttonUtilities);
});

module.exports = buttons;
