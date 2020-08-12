MWEditor.core = {
    button: function(config) {
        config = config || {};
        var defaults = {
            tag: 'button',
            props: {
                className: 'mdi mw-editor-controller-component mw-editor-controller-button',
                type: 'button'
            }
        };
        if (config.props && config.props.className){
            config.props.className = defaults.props.className + ' ' + config.props.className;
        }
        var settings = $.extend(true, {}, defaults, config);
        return mw.element(settings);
    },
    element: function(config) {
        config = config || {};
        var defaults = {
            props: {
                className: 'mw-editor-controller-component'
            }
        };
        var settings = $.extend(true, {}, defaults, config);
        var el = mw.element(settings);
        el.on('mousedown touchstart', function (e) {
            e.preventDefault();
        });
        return el;
    },

    _dropdownOption: function (data) {
        // data: { label: string, value: any },
        var option = MWEditor.core.element({
            props: {
                className: 'mw-editor-dropdown-option',
                innerHTML: data.label
            }
        });
        option.on('mousedown touchstart', function (e) {
            e.preventDefault();
        });
        option.value = data.value;
        return option;
    },
    dropdown: function (options) {
        var lscope = this;
        this.root = MWEditor.core.element();
        this.select = MWEditor.core.element({
            props: {
                className: 'mw-editor-controller-component mw-editor-controller-component-select'
            }
        });
        var displayValNode = MWEditor.core.button({
            props: {
                className: 'mw-editor-select-display-value'
            }
        });
        
        var valueHolder = MWEditor.core.element({
            props: {
                className: 'mw-editor-controller-component-select-values-holder'
            }
        });
        this.root.value = function (val){
            this.displayValue(val.label);
            this.value(val.value);
        };

        this.root.displayValue = function (val) {
            displayValNode.html(val);
        };

        this.select.append(displayValNode);
        this.select.append(valueHolder);
        for (var i = 0; i < options.data.length; i++) {
            var dt = options.data[i];
            var opt = MWEditor.core._dropdownOption(dt);
            opt.on('click', function (){
                lscope.select.trigger('change', dt);
            });
            valueHolder.append(opt);
        }

        this.select.on('click', function (){
            MWEditor.core._preSelect(this.node);
            this.toggleClass('active');
        });
        this.root.append(this.select);
    },
    _preSelect: function (node) {
        var all = document.querySelectorAll('.mw-editor-controller-component-select.active, .mw-bar-control-item-group.active');
        var i = 0, l = all.length;
        for ( ; i < l; i++) {
            if(!node || all[i] !== node) {
                all[i].classList.remove('active');
            }
        }
    }
};
