$(() => {
    $(document).on('click', '.navigator li', (e) => {
        const $eventContainer = $(e.currentTarget)
        const referenceNo = $eventContainer.data('reference');
        const $form = $eventContainer.parents('form');
        const $target = $form.find(`[data-navigator-reference="${referenceNo}"]`)

        console.info('clicked on navigator', referenceNo, $eventContainer, $target);

        if($target.length) {
            const position = $target.offset();

            window.scrollTo({
                top: position.top,
            });
        }
    });

    $(document).on('nestedfieldsitemadd', (e, params) => {
        const $addedItem = $(e.target);
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

        console.info('nested item add', e, { referenceNo, title, newReferenceNo, listToAdd });

        if(title) {
            //
            $addedItem.data('navigator-reference', newReferenceNo);
            // Data attribute does not get refreshed
            $addedItem.attr('data-navigator-reference', newReferenceNo);

            listToAdd.append(
                $('<li />')
                .data('reference', newReferenceNo)
                    .attr('data-reference', newReferenceNo)
                    .append($('<a />').text(title))
            );

            $(document).trigger('navigatoradd');
        }
    })
    
    $(document).on('nestedfieldsremove', e => {

    });

    const navState = new NavigatorState(document.querySelector('.navigator'));

    navState.registerEvents();
    navState.populateNavigableAreas();
    navState.refreshCurrent();
});

class NavigatorState {
    constructor(navigatorElement) {
        this.$navigator = $(navigatorElement);
        this.$navigatorArea = this.$navigator.parents('form');
        
        this.navigableAreas = [];
        this.activeScrollTimeout = null;
    }

    registerEvents() {
        $(window).on('scroll', (e) => {
            clearTimeout(this.activeScrollTimeout)

            this.activeScrollTimeout = setTimeout(() => {
                this.populateNavigableAreas();
                this.refreshCurrent();
            }, 50)
        });

        $(document).on('navigatoradd', e => {
            this.populateNavigableAreas();
            this.refreshCurrent();
        });
    }

    refreshCurrent() {
        this.$navigator.find('li').removeClass('active');

        console.info('Refreshing current', window.scrollY);

        const currentScrollTop = window.scrollY;
        const candidates = [];

        for(let value of this.navigableAreas) {
            const minimumVisibleHeight = value.rect.height / 2;
            const topEnd = value.rect.top + minimumVisibleHeight;

            console.info(value.element, topEnd);

            if(value.rect.top + minimumVisibleHeight > 0 &&
                value.rect.bottom > minimumVisibleHeight &&
                value.rect.bottom < window.innerHeight
            ) {
                candidates.push(value);
            }
        }

        console.info('ending closest', candidates, this.navigableAreas);

        if(candidates.length) {
            candidates[0].item.addClass('active');
        }
    }

    populateNavigableAreas() {
        const $elements = this.findNavigableElements();

        this.navigableAreas = [];

        $elements.each((index, element) => {
            const rect = element.getBoundingClientRect();
            const referenceNo = $(element).data('navigator-reference');
            const item = this.$navigator.find(`li[data-reference="${referenceNo}"]`);

            this.navigableAreas.push({
                rect,
                element,
                item
            });
        });

        return this.navigableAreas;
    }

    findNavigableElements() {
        return this.$navigatorArea.find('[data-navigator-reference]');
    }
}