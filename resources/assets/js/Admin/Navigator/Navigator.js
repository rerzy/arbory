import NavigatorItem from './Item'
import { Viewport }  from './Viewport'

export default class Navigator
{
    constructor()
    {
        this.element = $('.navigator')
        this.navigatorArea = this.element.parents('form')
        this.items = [];
        this.viewportManager = new Viewport(this);
    }

    /**
     * @returns {Boolean}
     */
    exists() {
        return this.element.length > 0;
    }

    init()
    {
        this.items = this.buildTreeFromList();
        this.viewportManager.init();
        this.registerEvents();
    }

    registerEvents()
    {
        this.element.on('click', '[data-reference]', this.onNavigatorClick.bind(this));

        $(document).on('nestedfieldsitemadd', e => this.parseNestedItem($(e.target)));
        $(document).on('nestedfieldsremove', this.onFieldRemove.bind(this));
        $(document).on('sortableupdated', this.onSort.bind(this));
        $(document).on('navigatoradd navigatorremove navigatorsort', e => {
            this.items = this.buildTreeFromList();
        });
    }

    onSort(e) {
        const $container = $(e.target);
        const children = $container.find('[data-navigator-reference]');

        children.each((index, item) => {
            const referenceNo = $(item).data('navigator-reference');
            const navigatorItem = this.findByReference(referenceNo);
            const targetList = navigatorItem.getListElement().parents('ul:first');
            
            navigatorItem.getListElement().insertBefore(targetList.find('> li').eq(index));
        });

        $(document).trigger('navigatorsort');
    }

    onNavigatorClick(e)
    {
        e.preventDefault()

        const referenceNo = $(e.currentTarget).data('reference')
        const item = this.findByReference(referenceNo)

        if (!item) {
            console.warn('Failed to find navigable element', e, this.items)
            return
        }

        const offset = item.getTargetElement().offset()

        $('html, body').animate({
            scrollTop: offset.top,
        })
    }

    onFieldRemove(e, { item, target_block }) {
        const referenceNo = item.data('navigator-reference');

        if (!item.length || !referenceNo) {
            console.error('Failed to find item for deletion', item);

            return;
        }

        const navigatorItem = this.findByReference(referenceNo);

        navigatorItem.getListElement().remove();

        $(document).trigger('navigatorremove');
    }

    /**
     * @returns {jQuery<HTMLElement>}
     */
    getElement() {
        return this.element;
    }

    /**
     * @returns {jQuery<HTMLElement>}
     */
    getNavigatorArea() {
        return this.navigatorArea;
    }

    /**
     * @returns {NavigatorItem[]}
     */
    getItems() {
        return this.items;
    }

    /**
     * @param {NavigatorItem} parent
     * @returns {Array}
     */
    buildTreeFromList(parent = null)
    {
        const parentElement = parent ? parent.getListElement().find('.children') : this.element

        let items = []

        parentElement.find('> [data-reference]').each((index, element) => {
            const $element = $(element)
            const referenceNo = $element.data('reference')
            const title = $(element).text()

            const item = new NavigatorItem(
                $element,
                this.findTargetElementByReference(referenceNo),
                referenceNo,
                title,
                parent,
            )

            items.push(item);

            if(item.hasChildren()) {
                items = items.concat(this.buildTreeFromList(item))
            }
        })

        return items
    }

    /**
     *
     * @param {String} referenceNo
     * @returns {jQuery}
     */
    findTargetElementByReference(referenceNo)
    {
        return this.navigatorArea.find(`[data-navigator-reference="${referenceNo}"]`)
    }

    /**
     *
     * @param referenceNo
     *
     * @returns {NavigatorItem}
     */
    findByReference(referenceNo)
    {
        const matches = this.items.filter(item => item.getReferenceNo() === referenceNo)

        return matches.length ? matches[0] : null
    }

    /**
     * @param {String} referenceNo
     * @param {String} title
     * @returns {jQuery}
     */
    createListItem(referenceNo, title)
    {
        const linkElement = $('<a />').text(title)

        return $('<li />').data('reference', referenceNo).attr('data-reference', referenceNo).append(linkElement)
    }

    /**
     * @param {jQuery<HTMLElement>} $addedItem
     */
    parseNestedItem($addedItem) {
        const referenceNo = $addedItem.data('navigator-reference');
        const $navigatorList = $('.navigator');

        let listToAdd = $navigatorList;

        if(! referenceNo) {
            const $rootItem = $addedItem.parents('[data-navigator-reference]');
            const parentReferenceNo = $rootItem.data('navigator-reference');

            if(! $rootItem.length) {
                console.warn('No parent found, not navigable');

                return;
            }

            listToAdd = $navigatorList.find(`li[data-reference="${parentReferenceNo}"] > ul`);
        }

        const title = $addedItem.data('title') || $addedItem.data('navigator-title');
        const newReferenceNo = btoa(String(Date.now()));

        if(title) {
            this.replaceReferenceNo($addedItem, newReferenceNo);
            listToAdd.append(this.createListItem(newReferenceNo, title));

            $(document).trigger('navigatoradd');
        }
    }

    replaceReferenceNo($element, referenceNo) {
        //
        $element.data('navigator-reference', referenceNo);
        // Data attribute does not get refreshed
        $element.attr('data-navigator-reference', referenceNo);
    }
}
