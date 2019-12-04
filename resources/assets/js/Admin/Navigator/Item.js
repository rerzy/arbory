
export default class NavigatorItem {

    constructor(listElement, targetElement, referenceNo, title, parent = null)
    {
        this.listElement = listElement;
        this.targetElement = targetElement;
        this.referenceNo = referenceNo;
        this.title = title;
        this.parent = parent;
    }

    /**
     *
     * @returns {NavigatorItem}
     */
    getParent() {
        return this.parent;
    }

    /**
     *
     * @returns {String}
     */
    getReferenceNo() {
        return this.referenceNo;
    }

    /**
     *
     * @returns {String}
     */
    getTitle() {
        return this.title;
    }

    /**
     * @returns {jQuery<HTMLElement>}
     */
    getListElement() {
        return this.listElement;
    }

    /**
     * @returns {jQuery<HTMLElement>}
     */
    getTargetElement() {
        return this.targetElement;
    }

    hasChildren() {
        return this.getListElement().find('> .children [data-reference]').length;
    }
}