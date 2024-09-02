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

const stored_signature = jQuery(`[name="mopar_company_signature"]`).val()
const opt = 800 > window.innerWidth ? {} : { ratio: 1 }
signature.fromDataURL(stored_signature, opt)

jQuery(`[name="clear_signature"]`).click(e => {
    signature.clear()
})

jQuery(`[name="mopar_company_settings"]`).submit(e => {
    jQuery(`[name="mopar_company_signature"]`).val(signature.toDataURL())
    return true
})

// ------------- Inititals
var initials = document.getElementById(`initials`)
function resizeInitials() {
    var ratio = Math.max(window.devicePixelRatio || 1, 1)
    initials.width = initials.offsetWidth * ratio
    initials.height = initials.offsetHeight * ratio
    initials.getContext("2d").scale(ratio, ratio)
}

window.onresize = resizeInitials
resizeInitials()

var initials = new SignaturePad(initials)

const stored_initials = jQuery(`[name="mopar_company_initials"]`).val()
initials.fromDataURL(stored_initials, opt)

jQuery(`[name="clear_initials"]`).click(e => {
    initials.clear()
})

jQuery(`[name="mopar_company_settings"]`).submit(e => {
    jQuery(`[name="mopar_company_initials"]`).val(initials.toDataURL())
    return true
})