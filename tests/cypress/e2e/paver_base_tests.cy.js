Cypress.Commands.add('getIframeBody', (iframeSelector) => {
    return cy
        .get(iframeSelector)
        .its('0.contentDocument.body')
        .should('not.be.empty')
        .then(cy.wrap)
})

describe('Base rendering test', () => {
    beforeEach(() => {
        cy.visit('http://localhost:8000')
    })

    it('renders the editor', () => {
        cy.get('.paver__container').should('be.visible')
        cy.get('.paver__editor').should('be.visible')
        cy.get('.paver__sidebar').should('be.visible')
    })

    it('renders the editor frame', () => {
        cy.getIframeBody('.paver__editor').within(() => {
            cy.get('.paver__editor-root').eq(0).should('be.visible')
            cy.get('.paver__editor-frame').eq(0).should('be.visible')
        })
    })
})

describe('Base rendering with content test', () => {
    beforeEach(() => {
        cy.visit('http://localhost:8000/?content=1')
    })

    it('renders the editor block in the frame', () => {
        cy.getIframeBody('.paver__editor').within(() => {
            cy.get('.paver__editor-root').eq(0).contains('This is an example')
        })
    })

    it('can enter a block edit state', () => {
        cy.getIframeBody('.paver__editor').within(() => {
            cy.wait(100).get('.paver__block-toolbar button').eq(1).click({force: true})
        })

        cy.get('.paver__component-sidebar').should('be.visible')
    })

    it('it can modify a block', () => {
        cy.getIframeBody('.paver__editor').within(() => {
            cy.wait(100).get('.paver__block-toolbar button').eq(1).click({force: true})
        })

        cy.get('.paver__component-sidebar').should('be.visible')
        cy.get('.paver__component-sidebar').find('.paver__option input').clear().type('John Smith')

        cy.getIframeBody('.paver__editor').within(() => {
            cy.get('.paver__editor-root').eq(0).contains('John Smith')
        })
    })
})
