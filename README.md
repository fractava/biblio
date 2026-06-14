# Biblio

[![PHPUnit GitHub Action](https://github.com/fractava/biblio/workflows/PHPUnit/badge.svg)](https://github.com/fractava/biblio/actions?query=workflow%3APHPUnit)
[![Node GitHub Action](https://github.com/fractava/biblio/workflows/Node/badge.svg)](https://github.com/fractava/biblio/actions?query=workflow%3ANode)
[![Lint GitHub Action](https://github.com/fractava/biblio/workflows/Lint/badge.svg)](https://github.com/fractava/biblio/actions?query=workflow%3ALint)

## Try it on your server
To install it change into your Nextcloud's apps directory:

    cd nextcloud/apps

Then run:

    git clone https://github.com/fractava/biblio biblio

Then install the dependencies using:

    make composer

## Frontend development

- 👩‍💻 Run `make dev-setup` to install the frontend dependencies
- 🏗 To build the Javascript whenever you make changes, run `make build-js`

To continuously run the build when editing source files you can make use of the `make watch-js` command.
