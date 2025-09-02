import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["step", "form", "progressBar"]
    static values = { currentStep: Number, totalSteps: Number }

    connect() {
        this.currentStepValue = 1
        this.totalStepsValue = 4
        this.showStep(1)
        this.updateProgressBar()
    }

    nextStep(event) {
        event.preventDefault()
        
        if (this.currentStepValue < this.totalStepsValue) {
            // Valider le formulaire actuel
            const currentForm = this.formTargets[this.currentStepValue - 1]
            if (currentForm.checkValidity()) {
                this.currentStepValue++
                this.showStep(this.currentStepValue)
                this.updateProgressBar()
            } else {
                currentForm.reportValidity()
            }
        }
    }

    previousStep(event) {
        event.preventDefault()
        
        if (this.currentStepValue > 1) {
            this.currentStepValue--
            this.showStep(this.currentStepValue)
            this.updateProgressBar()
        }
    }

    showStep(stepNumber) {
        this.stepTargets.forEach((step, index) => {
            if (index + 1 === stepNumber) {
                step.classList.remove('hidden')
                step.classList.add('block')
            } else {
                step.classList.remove('block')
                step.classList.add('hidden')
            }
        })
    }

    updateProgressBar() {
        const progress = (this.currentStepValue / this.totalStepsValue) * 100
        this.progressBarTarget.style.width = `${progress}%`
    }

    // Méthode pour soumettre le formulaire final
    submitForm(event) {
        event.preventDefault()
        
        // Valider tous les formulaires
        let allValid = true
        this.formTargets.forEach(form => {
            if (!form.checkValidity()) {
                allValid = false
                form.reportValidity()
            }
        })

        if (allValid) {
            // Soumettre le formulaire principal
            const mainForm = this.element.querySelector('form[data-main-form]')
            if (mainForm) {
                mainForm.submit()
            }
        }
    }
}
