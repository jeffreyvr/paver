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

it('can modify a block', () => {
    cy.getIframeBody('.paver__editor').within(() => {
        cy.wait(200).get('.paver__block-toolbar button').eq(1).click({force: true})
    })

    cy.get('.paver__component-sidebar').should('be.visible')
    cy.get('.paver__component-sidebar').find('.paver__option input').clear().type('John Smith')

    cy.getIframeBody('.paver__editor').within(() => {
        cy.get('.paver__editor-root').eq(0).contains('John Smith')
    })
})
