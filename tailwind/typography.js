module.exports = {
  typography: (theme) => ({
    DEFAULT: {
      css: {
        "--tw-prose-headings": theme("colors.primary.500"),
        "--tw-prose-body": theme("colors.black"),
        "--tw-prose-links": theme("colors.primary.600"),
        "--tw-prose-bold": "inherit",
        maxWidth: false,
        fontSize: theme("fontSize.base"),

        a: {
          "text-decoration": "none",
          "text-underline-offset": "0.425rem",
          "font-weight": 400,
          "&:hover": {
            "text-decoration": "underline",
          },
        },

        "h1, h2, h3, h4, h5, h6": {
          "font-weight": 700,
          textTransform: "normal",
          a: {
            "&:hover": {
              "text-decoration": "none",
            },
          },
        },

        h1: {
          fontSize: theme("fontSize.3xl"),
        },
        h2: {
          fontSize: theme("fontSize.2xl"),
        },
        h3: {
          fontSize: theme("fontSize.2xl"),
        },
      },
    },
    lg: {
      css: {
        fontSize: theme("fontSize.lg"),

        h1: {
          fontSize: theme("fontSize.4xl"),
        },
        h2: {
          fontSize: theme("fontSize.3xl"),
        },
        h3: {
          fontSize: theme("fontSize.2xl"),
        },
      },
    },
  }),
};
