# Biblio

[![PHPUnit GitHub Action](https://github.com/fractava/biblio/workflows/PHPUnit/badge.svg)](https://github.com/nextcloud/app-tutorial/actions?query=workflow%3APHPUnit)
[![Node GitHub Action](https://github.com/fractava/biblio/workflows/Node/badge.svg)](https://github.com/nextcloud/app-tutorial/actions?query=workflow%3ANode)
[![Lint GitHub Action](https://github.com/fractava/biblio/workflows/Lint/badge.svg)](https://github.com/nextcloud/app-tutorial/actions?query=workflow%3ALint)

## Try it on Gitpod
[![Open in Gitpod](https://gitpod.io/button/open-in-gitpod.svg)](https://gitpod.io/#https://github.com/fractava/biblio/)

It will automatically spin up and configure a full Nextcloud, MariaDB and PhpMyAdmin server.

### Nextcloud Login:
**Username:** dev

**Password:** t2qQ1C6ktYUv7

### PhpMyAdmin Login:
**Username:** nextcloud

**Password:** wdGq73jQB0p373gLdf6yLRj5

(It is fine to have these static logins, because gitpod has acess control built in and no sensitive data is stored in these dev servers)

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
