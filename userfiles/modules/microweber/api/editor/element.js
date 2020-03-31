(function(){
    var Element = function(options){

        options = options || {};

        var defaults = {
            tag: 'div',
            props: {},
            document: document,
            register: null
        };

        this.settings = $.extend({}, defaults, options);

        this.document = this.settings.document || document;

        this.register = function(){
            if(this.settings.register) {
                var reg = this.settings.register;
                reg.push(this);
            }
        };

        this.create = function(){
            this.node = this.document.createElement(this.settings.tag);
            this.$node = $(this.node);
        };

        this.setProps = function(){
            for(var i in this.settings.props) {
                this.node[i] = this.settings.props[i];
            }
        };

        this.prop = function(prop, val){
            if(this.node[prop] !== val){
                this.node[prop] = val;
                this.$node.trigger('propChange', [prop, val, Element]);
            }
        };

        this.init = function(){
            this.create();
            this.setProps();
            this.register();
        };
        this.init();
    };
    mw.element = function(options){
        return new Element(options);
    };
})();
