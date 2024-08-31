const { defineConfig } = require("cypress");

module.exports = defineConfig({
  e2e: {
    experimentalStudio: true,
    specPattern: 'tests/cypress/e2e/**/*.cy.{js,jsx,ts,tsx}',
    fixturesFolder: false,
    supportFile: false,
    screenshotsFolder: false,

    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
});
