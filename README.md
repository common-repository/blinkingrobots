# Boilerplate Addon

## Requirements

* `nodejs` installed

* `composer` installed
* `PHP 7.0` installed on your local machine

## Setup

1. Replace folder name **blinkingrobots**  with the plugin folder name you are going to develop
1. Change **blinkingrobots** in `/package.json` file with the plugin folder name you are going to develop
1. Change **Blinking Robots**, **Blinking Robots**
1. Change **This pluign provides an ability to fetch articles from the given feeds and summarize them with OpenAI**
1. Change **blinkingrobots**, and **{Plugin_URI}**
1. Change all occurrences of namespace **BlinkingRobots** with your desire namespace
1. Change all occurrences of **blinkingrobots** with the plugin's name which should be the same as folder name
1. Change blinkingrobots.pot file's which should be the same as folder name
1. Change **window.blinkingrobots** with the actual prefix in the `src/js/utilities/loadScripts.js` and `src/js/utilities/DeferJS.js`

### ACF Pro

1. In case the plugin depends on ACF Pro plugin you should define it in the `/bootstrap.php` file
    * For enabling ACF Pro checking `define(__NAMESPACE__ . '\ACF_REQUIRED', true);`

### Gravity Forms

In case the plugin depends on Gravity Forms you should define it in the `/bootstrap.php` file

* For enabling GF support and Settings page `define(__NAMESPACE__ . '\GFORMS_ADDON', true);`
* Then replace **GF_Addon_Name** with a `One_Word` name over the project
* Then replace **GF_Class_Name** with a `GFPluginNameAddon` class name over the project

:warning: Before initially release a new plugin, please eliminate all text above.

---

# Blinking Robots [![js-standard-style](https://img.shields.io/badge/code%20style-standard-brightgreen.svg)](http://standardjs.com)

This pluign provides an ability to fetch articles from the given feeds and summarize them with OpenAI

## Get started

1. Clone the repository to plugins folder
    ```
    > cd wp-content/plugins
    > git clone https://github.com/hisaveliy/blinkingrobots
    ```
1. Run NPM packages installation
    ``` 
    > npm install
    ```

## Bundle assets

**Proceed with development**

* run npm watcher for development. **Note:** after the initial build, webpack will continue to watch for changes in any
  of the resolved files and it will sync the root folder
   ``` 
   > npm run watch
   ```
* run building command for production
   ``` 
   > npm run prod
   ```

## Version control

Follow the rules for applying your changes to master branch.

### How to contribute

1. Each feature, improvement or bug fix should be completed in a dedicated branch, but it is possible to complete
   several Trello cards under the same branch if the are logically connected.
1. Iterate version
    - in `style.css` for theme development
    - in `bootstrap.php` for plugin development
1. Make sure:
    - a task is properly completed
    - a branch is fully tested
    - code written according to [Code style](#code-style)
1. Create a pull-request which must be named and described according
   to [Commit and Pull-request standards](#commit-and-pull-request-standards)
1. Complete merging the branch with master through Github
