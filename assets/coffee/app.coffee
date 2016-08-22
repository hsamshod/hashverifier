$(document).ready ->
    window.vm = new Vue
        el: '#app',

        data:
            disabled: true
            captchaInput: ''
            showform: false
            hidemore: true

        methods:
            verifyCaptcha: ->
                console.log 'verify'
                this.disabled = false if grecaptcha.getResponse()

            handleClick: (event) ->
                event.preventDefault() if this.disabled

            toggleForm: ->
                this.showform = !this.showform
                this.hidemore = !this.hidemore

    window.verifyCaptcha = ->
        vm.verifyCaptcha()