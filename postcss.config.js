/* eslint-env node */

module.exports = {
    plugins: [
        require("postcss-import-ext-glob"),
        require("@tailwindcss/postcss"),
        require("./scripts/theme-json-root"),
    ],
};
