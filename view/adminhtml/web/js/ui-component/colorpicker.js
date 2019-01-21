/*
 * Copyright © 2018 Studio Raz. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'jquery/colorpicker/js/colorpicker'
], function ($, ElementAbstract) {
    'use strict';

    return ElementAbstract.extend({
        defaults: {
            elementTmpl: 'SR_ThemeConfigurator/ui-component/colorpicker',
        },

        /**
         * Initialize.
         *
         * @returns {Object} Chainable.
         */
        initialize: function () {
            this._super();

            this.initializeColorPicker();

            return this;
        },

        /**
         * Initializes jq ColorPicker plugin (on-click)
         */
        initializeColorPicker: function() {
            let self = this;

            $(document).on('click', '#' + this.uid, function (e) {
                let $target = $(e.currentTarget);

                let colorPicker = $target.ColorPicker({
                    color: self.value(),

                    onChange: function (hsb, hex, rgb) {
                        let colorHEX = "#" + hex;

                        // DOM element
                        $target.css('background-color', colorHEX);

                        // ui Component
                        self.value(colorHEX);
                    }
                });

                colorPicker.ColorPickerShow();
            });
        }
    });
});

