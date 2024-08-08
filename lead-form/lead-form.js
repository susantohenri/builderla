const builderla_lead_form = jQuery(`#builderla_lead_form`)
const message = builderla_lead_form.find(`#message`)
const submit_btn = builderla_lead_form.find(`#submit_btn`)
message.parent().hide()
builderla_lead_form.find(`[builderla-lead-form-step]`).hide()
builderla_lead_form.find(`[builderla-lead-form-step="1"]`).show()

submit_btn.click(e => {
    e.preventDefault()

    const current_step = parseInt(builderla_lead_form.find(`[builderla-lead-form-step]:visible`).attr(`builderla-lead-form-step`))
    let next_step = current_step + 1

    let valid = true
    let answers = {}
    builderla_lead_form
        .find(`[builderla-lead-form-step="${current_step}"]`)
        .find(`input, select, textarea`)
        .each((index, element) => {
            const field = jQuery(element)
            const name = field.attr(`name`)
            const required = `true` == field.attr(`aria-required`)
            const answer = field.val()

            if (required && `` == answer) {
                const placeholder = field.attr(`placeholder`)
                message.html(`${placeholder} is required`).css(`color`, `red`).parent().show()
                valid = false
                return false
            } else answers[name] = answer
        })

    if (valid) {
        answers.step = current_step
        answers.action = builderla_lead_form_obj.action
        jQuery.post(builderla_lead_form_obj.url, answers, resp => {

            switch (current_step) {
                case 1: builderla_lead_form.find(`[name="cliente_id"]`).val(resp); break
                case 2: builderla_lead_form.find(`[name="vehiculo_id"]`).val(resp); break
            }

            if (0 < jQuery(`[builderla-lead-form-step="${next_step}"]`).length) {
                message.html(``).parent().hide()

                builderla_lead_form.find(`[builderla-lead-form-step="${current_step}"]`).hide()
                builderla_lead_form.find(`[builderla-lead-form-step="${next_step}"]`).show()

                next_step++
                if (1 > jQuery(`[builderla-lead-form-step="${next_step}"]`).length) submit_btn.val(`Finish`)
            } else {
                message.html(`Thank you for your message. It has been sent.`).css(`color`, `inherit`).parent().show()
                builderla_lead_form.find(`[builderla-lead-form-step]`).hide()
                submit_btn.hide()
            }
        })
    }

})