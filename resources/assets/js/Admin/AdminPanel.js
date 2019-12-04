export default class AdminPanel {
    /**
     * @param {FieldRegistry} registry
     * @param {Navigator} navigator
     */
    constructor(registry, navigator) {
        this.navigator = navigator
        this.registry = registry;
    }


    /**
     * @param {Navigator} navigator
     */
    set navigator(navigator) {
        this._navigator = navigator;
    }

    /**
     * @return {Navigator}
     */
    get navigator() {
        return this._navigator;
    }

    /**
     * @param {FieldRegistry} registry
     */
    set registry(registry) {
        this._registry = registry;
    }

    /**
     * @return {FieldRegistry}
     */
    get registry() {
        return this._registry;
    }

    /**
     * @return {void}
     */
    initialize() {
        CKEDITOR.basePath = '/arbory/ckeditor/';

        CKEDITOR.on('instanceReady', function(e) {
            jQuery(e.editor.element.$).addClass("ckeditor-initialized");
        });

        this.registerEventHandlers();
    }

    /**
     * @return {void}
     */
    registerEventHandlers() {
        let body = jQuery('body');

        body.on('nestedfieldsitemadd', 'section.nested', event => {
            this.initializeFields(event.target);
        });

        body.ready(() => {
            this.initializeFields(body[0]);

            if(this.navigator.exists()) {
                this.navigator.init();
            }
        });
    }

    /**
     * @return {void}
     */
    initializeFields(scope) {
        for (let [_, definition] of Object.entries(this.registry.definitions)) {
            jQuery(scope).find(definition.selector).each((key, element) => {
                new definition.handler(element, definition.config);
            });
        }
    }
}