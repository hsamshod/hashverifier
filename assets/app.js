$(document).ready(function() {
    var vm = new Vue({
        el: '#app',
        data: {
            disabled: true,
            captchaInput: ''
        },
        
        methods: {
            handleClick: function (event) {
                if (this.disabled) {
                    event.preventDefault();
                }
            }
        },
        computed: {
            disabled: function () {
                return this.captchaInput.length < 6
            }
        }

    });
});