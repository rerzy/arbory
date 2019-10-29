import "is-in-viewport";

export class Viewport {
    /**
     * @param {Navigator} navigator
     * @param {Number} tolerance
     */
    constructor(navigator, tolerance = 65) {
        this.navigator = navigator;
        this.$navigator = navigator.getElement();

        this.navigableAreas = [];
        this.activeScrollTimeout = null;
        this.tolerance = tolerance;
    }

    registerEvents() {
        $(window).on('scroll', (e) => {
            clearTimeout(this.activeScrollTimeout)

            this.activeScrollTimeout = setTimeout(() => {
                this.addActiveState();
            }, 50)
        });

        $(document).on('navigatoradd navigatorremove', e => {
            this.addActiveState();
        });
    }

    init() {
        this.registerEvents();
        this.addActiveState();
    }

    addActiveState() {
        this.$navigator.find('li').removeClass('active');

        const candidates = [];

        for(let value of this.navigator.getItems()) {
            const $element = value.getTargetElement();
            const hasChildren = value.hasChildren();
            const height = $element.height();
            const visibilityRatio = height * this.tolerance / 100 * -1;
            const inViewport = $element.is(`:in-viewport(${visibilityRatio})`)


            if(inViewport && ! hasChildren) {
                if(value.getParent()) {
                    candidates.push(value.getParent());
                }

                candidates.push(value);
            }
        }

        if(candidates.length) {
            candidates.forEach((candidate) => candidate.getListElement().addClass('active'));
        }
    }
}