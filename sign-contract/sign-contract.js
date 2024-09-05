jQuery(document).ready(() => {
    jQuery('[name="signed_date"]').datetimepicker({
        format: `MM/DD/YYYY`
    })
    jQuery('[name="client_dob"]').datetimepicker({
        format: `MM/DD/YYYY`
    })

    // ------------- Signature
    var signature = document.getElementById(`signature`)
    function resizeSignature() {
        var ratio = Math.max(window.devicePixelRatio || 1, 1)
        signature.width = signature.offsetWidth * ratio
        signature.height = signature.offsetHeight * ratio
        signature.getContext("2d").scale(ratio, ratio)
    }

    window.onresize = resizeSignature
    resizeSignature()

    var signature = new SignaturePad(signature)

    const stored_signature = jQuery(`[name="client_signature"]`).val()
    const opt = 800 > window.innerWidth ? {} : { ratio: 1 }
    signature.fromDataURL(stored_signature, opt)

    jQuery(`[name="clear_signature"]`).click(e => {
        e.preventDefault()
        signature.clear()
    })

    jQuery(`[name="sign_contract"]`).submit(e => {
        jQuery(`[name="client_signature"]`).val(signature.toDataURL())
        return true
    })

    // ------------- Initital
    var initial = document.getElementById(`initial`)
    function resizeinitial() {
        var ratio = Math.max(window.devicePixelRatio || 1, 1)
        initial.width = initial.offsetWidth * ratio
        initial.height = initial.offsetHeight * ratio
        initial.getContext("2d").scale(ratio, ratio)
    }

    window.onresize = resizeinitial
    resizeinitial()

    var initial = new SignaturePad(initial)

    const stored_initial = jQuery(`[name="client_initial"]`).val()
    initial.fromDataURL(stored_initial, opt)

    jQuery(`[name="clear_initial"]`).click(e => {
        e.preventDefault()
        initial.clear()
    })

    jQuery(`[name="sign_contract"]`).submit(e => {
        jQuery(`[name="client_initial"]`).val(initial.toDataURL())
        return true
    })
})