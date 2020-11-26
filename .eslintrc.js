module.exports = {
  globals: {
    aoat_config: "readonly"
  },

  root: true,
  env: {
    browser: true,
    node: true
  },
  parserOptions: {
    parser: "babel-eslint"
  },
  extends: [
    "eslint:recommended",
    "plugin:vue/recommended",
    "plugin:prettier/recommended",
    "prettier/vue"
  ],
  // required to lint *.vue files
  plugins: ["vue", "prettier"],
  // add your custom rules here
  rules: {
    "prettier/prettier": "error",
    "no-console": [
      process.env.NODE_ENV === "development" ? "off" : "error",
      { allow: ["warn", "error"] }
    ],
    "no-debugger": process.env.NODE_ENV === "development" ? "off" : "error",
    "no-unreachable": process.env.NODE_ENV === "development" ? "off" : "error",
    "no-unused-vars": process.env.NODE_ENV === "development" ? "off" : "error",
    "vue/no-unused-vars":
      process.env.NODE_ENV === "development" ? "off" : "error",
    "vue/no-unused-components":
      process.env.NODE_ENV === "development" ? "off" : "error",
    "vue/max-attributes-per-line": "off",
    "vue/no-v-html": "off",
    "vue/html-self-closing": [
      "warn",
      {
        html: {
          void: "any",
          normal: "always",
          component: "always"
        },
        svg: "always",
        math: "always"
      }
    ]
  }
};
