# EASYTOOLTIP FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

## Features

EasyTooltip enriches the informative tooltips Dolibarr shows when you hover a
link to an object (order, invoice, quote, product, third party, user, bank
account, member, ticket, survey, project...). For each object type it can add,
on top of the standard tooltip content:

- The public and private notes of the object.
- For products/services: the full description, the service duration, the
  latest customer orders, the latest supplier orders, and the stock per
  warehouse.

Each of these extra blocks can be enabled or disabled independently per
object type from the module setup page.

The module also provides two additional CAPTCHA drivers, selectable from
`Home - Setup - Security - Captcha code`:

- **EasyTooltip**: a simple image CAPTCHA (GD-generated, like the Dolibarr
  standard one but with mixed colors and rotation).
- **EasyTooltip advanced**: an image-selection CAPTCHA based on the
  [IconCaptcha](https://github.com/fabianwennink/IconCaptcha-PHP) library,
  where the user has to click the icon that appears the least amount of
  times.

Finally, the module ships FontAwesome 7 (free) and, when activated,
automatically switches Dolibarr's icon set to it (constant
`MAIN_FONTAWESOME_DIRECTORY`), instead of the FontAwesome 5 bundled with
Dolibarr core.

Other external modules are available on [Dolistore.com](https://www.dolistore.com).

## Translations

Translations can be completed manually by editing files into directories *langs*.

<!--
This module contains also a sample configuration for Transifex, under the hidden directory [.tx](.tx), so it is possible to manage translation using this service.

For more informations, see the [translator's documentation](https://wiki.dolibarr.org/index.php/Translator_documentation).

There is a [Transifex project](https://transifex.com/projects/p/dolibarr-module-template) for this module.
-->

<!--

## Installation

### From the ZIP file and GUI interface

If the module is a ready to deploy zip file, so with a name module_xxx-version.zip (like when downloading it from a market place like [Dolistore](https://www.dolistore.com)),
go into menu ```Home - Setup - Modules - Deploy external module``` and upload the zip file.

Note: If this screen tell you that there is no "custom" directory, check that your setup is correct:

- In your Dolibarr installation directory, edit the ```htdocs/conf/conf.php``` file and check that following lines are not commented:

    ```php
    //$dolibarr_main_url_root_alt ...
    //$dolibarr_main_document_root_alt ...
    ```

- Uncomment them if necessary (delete the leading ```//```) and assign a sensible value according to your Dolibarr installation

    For example :

    - UNIX:
        ```php
        $dolibarr_main_url_root_alt = '/custom';
        $dolibarr_main_document_root_alt = '/var/www/Dolibarr/htdocs/custom';
        ```

    - Windows:
        ```php
        $dolibarr_main_url_root_alt = '/custom';
        $dolibarr_main_document_root_alt = 'C:/My Web Sites/Dolibarr/htdocs/custom';
        ```

### From a GIT repository

Clone the repository in ```$dolibarr_main_document_root_alt/easytooltip```

```sh
cd ....../custom
git clone git@github.com:gitlogin/easytooltip.git easytooltip
```

### <a name="final_steps"></a>Final steps

From your browser:

  - Log into Dolibarr as a super-administrator
  - Go to "Setup" -> "Modules"
  - You should now be able to find and enable the module

-->

## Licenses

### Main code

GPLv3 or (at your option) any later version. See file COPYING for more information.

### Documentation

All texts and readmes are licensed under GFDL.
